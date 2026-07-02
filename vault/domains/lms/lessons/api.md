---
domain: lms
module: lessons
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Lessons — API / DTOs

## `CreateLessonData`

| Field | Type | Rules |
|---|---|---|
| `module_id` | ulid | required, exists in company |
| `title` | string | required |
| `type` | enum | in: video, text, file, quiz |
| `content` | jsonb | schema-validated per type; embed URLs whitelisted to youtube/vimeo |
| `completion_criteria` | enum | in: viewed, quiz-passed |
| `duration_minutes` | int | nullable, min:0 |

## `SubmitQuizData` (learner)

| Field | Type | Rules |
|---|---|---|
| `lesson_id` | ulid | required; learner must be enrolled |
| `answers` | array | required; graded server-side |

- Response is `QuizResult` — the request never contains and the response never returns correct-answer flags.

## `QuizResult` (output)

| Field | Type | Notes |
|---|---|---|
| `score` | int | 0–100 this attempt |
| `passed` | bool | `score >= passing_score` |
| `best` | int | Best score retained |

## Learner / Portal Endpoints

- Lesson player + quiz submission live on the [[../enrolments/_module\|enrolments]] Vue+Inertia learner portal (`/learn`), behind the scoped learner guard. Lessons exposes no standalone public route; the portal calls `LessonProgressService` / `QuizService`.
