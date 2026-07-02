---
domain: lms
module: courses
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Courses — Data Model

## `lms_courses`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `title` | string | |
| `slug` | string | Unique per company (`spatie/laravel-sluggable`) |
| `description` | text | Purified |
| `category` | string nullable | |
| `instructor_id` | ulid nullable | FK → `users` |
| `status` | string | draft / published / archived (default `draft`) |
| `enrolment_type` | string | open / invite / mandatory |
| `audience` | string | internal / external / both *(assumed)* (default `internal`) |
| `prerequisites` | jsonb | Course ids, cycle-checked (default `[]`) |
| `estimated_minutes` | int nullable | |
| `certificate_template_id` | ulid nullable | FK → `lms_certificate_templates` |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Indexes:** `(company_id, status)`, unique `(company_id, slug)`.

## `lms_course_modules`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `course_id` | ulid | FK → `lms_courses` |
| `title` | string | |
| `order` | int | Drag-drop ordering |

## ERD

```mermaid
erDiagram
    lms_courses ||--o{ lms_course_modules : contains
    lms_course_modules ||--o{ lms_lessons : contains
    lms_courses }o--o| lms_certificate_templates : "issues via"
    lms_courses ||--o{ lms_enrolments : "enrolled in"
    users ||--o{ lms_courses : instructs

    lms_courses {
        ulid id PK
        ulid company_id
        string title
        string slug
        text description
        string category
        ulid instructor_id FK
        string status
        string enrolment_type
        string audience
        jsonb prerequisites
        int estimated_minutes
        ulid certificate_template_id FK
        timestamp deleted_at
    }
    lms_course_modules {
        ulid id PK
        ulid company_id
        ulid course_id FK
        string title
        int order
    }
```

`lms_lessons`, `lms_enrolments`, `lms_certificate_templates` are owned by sibling modules — shown for context only.
