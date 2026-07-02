---
domain: lms
module: learning-paths
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Learning Paths — Data Model

## `lms_paths`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `title` | string | |
| `description` | text | |
| `sequential` | bool | Sequential unlock vs parallel |
| `certificate_template_id` | ulid nullable | Path-completion certificate |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `lms_path_courses`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `path_id` | ulid | FK → `lms_paths` |
| `course_id` | ulid | FK → `lms_courses` |
| `order` | int | Sequence position |

**Unique:** `(path_id, course_id)`.

## `lms_path_enrolments`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `path_id` | ulid | FK → `lms_paths` |
| `learner_type` / `learner_id` | string / ulid | employee / external |
| `progress_percent` | decimal(5,2) | Courses completed / total |
| `completed_at` | timestamp nullable | |

**Unique:** active `(path_id, learner_type, learner_id)`.

## ERD

```mermaid
erDiagram
    lms_paths ||--o{ lms_path_courses : "ordered"
    lms_courses ||--o{ lms_path_courses : "included in"
    lms_paths ||--o{ lms_path_enrolments : "enrolled in"
    lms_paths }o--o| lms_certificate_templates : "certifies via"

    lms_paths {
        ulid id PK
        ulid company_id
        string title
        text description
        bool sequential
        ulid certificate_template_id FK
        timestamp deleted_at
    }
    lms_path_courses {
        ulid id PK
        ulid company_id
        ulid path_id FK
        ulid course_id FK
        int order
    }
    lms_path_enrolments {
        ulid id PK
        ulid company_id
        ulid path_id FK
        string learner_type
        ulid learner_id
        decimal progress_percent
        timestamp completed_at
    }
```

`lms_courses` / `lms_certificate_templates` owned by sibling modules — shown for context.
