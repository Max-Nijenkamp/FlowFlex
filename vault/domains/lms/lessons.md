---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.lessons
status: planned
priority: p3
depends-on: [lms.courses, core.billing, core.rbac, core.files]
soft-depends: []
fires-events: []
consumes-events: []
patterns: []
tables: [lms_lessons, lms_quizzes, lms_lesson_progress]
permission-prefix: lms.lessons
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Lessons & Content

Individual lessons within a course module: video, text, file attachments, and quizzes.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/lms/courses\|lms.courses]] | lessons live in course modules |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, video/file uploads |

---

## Core Features

- Lesson record: title, module, type, content, order, duration
- Lesson types: video, text/article (Tiptap, purified), file/download, quiz (SCORM later *(assumed)*)
- Video: upload (tenant-scoped, streamed via signed URL) or embed (YouTube/Vimeo URL-validated)
- Quiz: questions (multiple choice, true/false), passing score, max attempts *(assumed: unlimited default)*
- Completion criteria per lesson: viewed / quiz-passed
- Lesson progress tracking per enrolment
- Downloadable resources per lesson

---

## Data Model

### lms_lessons — id, module_id FK, course_id FK, company_id (indexed), title, type (in set), content (jsonb per type: {body}|{video_media_id|embed_url}|{file_media_id}|{quiz_id}), order, duration_minutes, completion_criteria (viewed/quiz-passed)
### lms_quizzes — id, lesson_id FK, company_id, questions (jsonb [{question, type, options[], correct}]), passing_score (0–100)
### lms_lesson_progress

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), lesson_id FK, enrolment_id FK | ulid | unique `(lesson_id, enrolment_id)` |
| status | string default `not-started` | not-started/in-progress/completed |
| completed_at | timestamp nullable | |
| quiz_score | int nullable | best score |
| attempts | int default 0 | |

---

## DTOs

### CreateLessonData — module_id, title, type (in set), content (schema-validated per type; embed URLs whitelisted to youtube/vimeo), completion_criteria, duration_minutes?
### SubmitQuizData (learner) — lesson_id (enrolled), answers[] — graded server-side, answers never expose correct flags client-side

## Services & Actions

- `LessonProgressService::complete(enrolmentId, lessonId)` — criteria check; recomputes enrolment progress (direct same-domain call into enrolments)
- `QuizService::grade(SubmitQuizData): QuizResult` — score, pass/fail, best-score retained

---

## Filament

**Nav group:** Courses

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| Lesson relation manager | on course modules | type-specific forms, quiz repeater |

Learner lesson player: Vue + Inertia learner portal.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('lms.lessons.view-any') && BillingService::hasModule('lms.lessons')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): Add upload constraints to the lesson video/file content section: allowed MIME/type whitelist, max file size, and companies/{company_id}/ storage path.

---

## Permissions

`lms.lessons.manage` (under course permissions)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Content schema per type enforced; non-whitelisted embed rejected
- [ ] Quiz graded server-side; correct answers never serialized to client
- [ ] Pass below passing_score = lesson not complete (quiz-passed criteria)
- [ ] Progress unique per (lesson, enrolment); best score kept
- [ ] Video served via signed URL

---

## Build Manifest

```
database/migrations/xxxx_create_lms_lessons_table.php
database/migrations/xxxx_create_lms_quizzes_table.php
database/migrations/xxxx_create_lms_lesson_progress_table.php
app/Models/LMS/{Lesson,Quiz,LessonProgress}.php
app/Data/LMS/{CreateLessonData,SubmitQuizData,QuizResult}.php
app/Services/LMS/{LessonProgressService,QuizService}.php
database/factories/LMS/{LessonFactory,QuizFactory}.php
tests/Feature/LMS/{LessonTest,QuizGradingTest}.php
```

---

## Related

- [[domains/lms/courses]]
- [[domains/lms/enrolments]]
