---
domain: lms
module: lessons
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Lessons — Architecture

## Lesson Types & Content Schema

`content` is a per-type jsonb blob, schema-validated on write:

| Type | `content` shape | Completion criterion |
|---|---|---|
| video | `{video_media_id}` or `{embed_url}` (whitelisted youtube/vimeo) | viewed |
| text | `{body}` (Tiptap, purified) | viewed |
| file | `{file_media_id}` | viewed |
| quiz | `{quiz_id}` | quiz-passed |

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\LMS\LessonProgressService` | service | `complete(enrolmentId, lessonId)` — checks criterion, writes `lms_lesson_progress`, then calls `EnrolmentService::recomputeProgress` (same-domain). |
| `App\Services\LMS\QuizService` | service | `grade(SubmitQuizData): QuizResult` — server-side scoring, pass/fail, best-score retained. Correct-answer flags never leave the server. |

### complete flow

1. Resolve the lesson's completion criterion.
2. For quiz lessons, require a passing `QuizResult` (score ≥ `passing_score`).
3. Upsert `lms_lesson_progress` for `(lesson_id, enrolment_id)` → `completed`, stamp `completed_at`.
4. Call `EnrolmentService::recomputeProgress(enrolmentId)` — enrolments recomputes % and may trigger completion side effects.

### grade flow

1. Load quiz by `lesson_id`; verify learner is enrolled.
2. Score answers server-side against `questions[].correct`.
3. Compare to `passing_score`; increment `attempts`; retain best `quiz_score`.
4. Return `QuizResult {score, passed, best}` — no correct-answer data serialized to client.

## Events

None. `LessonProgressService` calls into `EnrolmentService` directly (same domain).

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| Lesson relation manager | Courses (on course modules) | #1-adjacent | Type-specific forms, quiz repeater. |
| Quiz authoring page | Courses | #3-style custom page | Question builder, answer keys, passing score. |

Learner lesson player: [[../enrolments/_module\|enrolments]] Vue+Inertia portal.

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.lessons.view-any')
        && BillingService::hasModule('lms.lessons');
}
```

## Jobs & Scheduling

None.

## Search & Realtime

Video served via signed URL from core.files. No realtime.
