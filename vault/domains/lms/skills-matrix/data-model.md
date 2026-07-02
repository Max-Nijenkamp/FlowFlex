---
domain: lms
module: skills-matrix
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Skills Matrix — Data Model

## `lms_skills`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `name` | string | Unique per company |
| `category` | string | technical / soft / compliance |

## `lms_employee_skills`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `employee_id` | ulid | FK → hr employee (`users`) |
| `skill_id` | ulid | FK → `lms_skills` |
| `proficiency_level` | int | 0–3 enum |
| `assessor_type` | string | self / manager |
| `assessed_by` | ulid | User id of the assessor |
| `assessed_at` | timestamp | |

**Unique:** `(employee_id, skill_id, assessor_type)` *(assumed: one row per assessor type)*.

## `lms_role_skills`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `role_name` | string | Role/position |
| `skill_id` | ulid | FK → `lms_skills` |
| `required_level` | int | 0–3 |

**Unique:** `(role_name, skill_id)`.

## `lms_course_skills`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `course_id` | ulid | FK → `lms_courses` |
| `skill_id` | ulid | FK → `lms_skills` |
| `taught_level` | int | Level a completion confers |

## ERD

```mermaid
erDiagram
    lms_skills ||--o{ lms_employee_skills : "assessed as"
    lms_skills ||--o{ lms_role_skills : "required by role"
    lms_skills ||--o{ lms_course_skills : "taught by course"
    lms_courses ||--o{ lms_course_skills : teaches
    users ||--o{ lms_employee_skills : "holds"

    lms_skills {
        ulid id PK
        ulid company_id
        string name
        string category
    }
    lms_employee_skills {
        ulid id PK
        ulid company_id
        ulid employee_id FK
        ulid skill_id FK
        int proficiency_level
        string assessor_type
        ulid assessed_by
        timestamp assessed_at
    }
    lms_role_skills {
        ulid id PK
        ulid company_id
        string role_name
        ulid skill_id FK
        int required_level
    }
    lms_course_skills {
        ulid id PK
        ulid company_id
        ulid course_id FK
        ulid skill_id FK
        int taught_level
    }
```

`lms_courses` owned by courses; `users`/employee owned by HR — shown for context.
