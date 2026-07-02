---
domain: lms
module: lessons
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Lessons — Data Model

## `lms_lessons`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `module_id` | ulid | FK → `lms_course_modules` |
| `course_id` | ulid | FK → `lms_courses` (denormalised for scoping) |
| `title` | string | |
| `type` | string | video / text / file / quiz |
| `content` | jsonb | Per-type shape (see [[architecture]]) |
| `order` | int | Within module |
| `duration_minutes` | int nullable | |
| `completion_criteria` | string | viewed / quiz-passed |

## `lms_quizzes`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `lesson_id` | ulid | FK → `lms_lessons` |
| `questions` | jsonb | `[{question, type, options[], correct}]` — `correct` never serialized to learner |
| `passing_score` | int | 0–100 |

## `lms_lesson_progress`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `lesson_id` | ulid | FK → `lms_lessons` |
| `enrolment_id` | ulid | FK → `lms_enrolments` |
| `status` | string | not-started / in-progress / completed (default `not-started`) |
| `completed_at` | timestamp nullable | |
| `quiz_score` | int nullable | Best score |
| `attempts` | int | Default 0 |

**Unique:** `(lesson_id, enrolment_id)`.

## ERD

```mermaid
erDiagram
    lms_course_modules ||--o{ lms_lessons : contains
    lms_lessons ||--o| lms_quizzes : "has"
    lms_lessons ||--o{ lms_lesson_progress : "tracked by"
    lms_enrolments ||--o{ lms_lesson_progress : "records"

    lms_lessons {
        ulid id PK
        ulid company_id
        ulid module_id FK
        ulid course_id FK
        string title
        string type
        jsonb content
        int order
        int duration_minutes
        string completion_criteria
    }
    lms_quizzes {
        ulid id PK
        ulid company_id
        ulid lesson_id FK
        jsonb questions
        int passing_score
    }
    lms_lesson_progress {
        ulid id PK
        ulid company_id
        ulid lesson_id FK
        ulid enrolment_id FK
        string status
        timestamp completed_at
        int quiz_score
        int attempts
    }
```

`lms_course_modules` (courses) and `lms_enrolments` (enrolments) are owned by sibling modules — shown for context.
