---
domain: lms
module: mentoring
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Mentoring — Data Model

## `lms_mentor_profiles`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `employee_id` | ulid | FK → employee (`users`), unique |
| `expertise` | jsonb | Tags (skills-fed or manual) |
| `availability` | string | Free text |
| `is_accepting` | bool | Open to new mentees |

## `lms_mentorships`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `mentor_id` | ulid | FK → employee |
| `mentee_id` | ulid | FK → employee (≠ mentor) |
| `focus_area` | string | |
| `goals` | jsonb | `[{title, done}]` |
| `status` | string | active / paused / completed |
| `started_at` | timestamp | |
| `ended_at` | timestamp nullable | |

**Unique:** active `(mentor_id, mentee_id)`.

## `lms_mentorship_sessions`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `mentorship_id` | ulid | FK → `lms_mentorships` |
| `session_date` | date | ≤ today |
| `notes` | text | **Pair-only** visibility |
| `action_items` | jsonb | |
| `rating` | int nullable | Optional session feedback |

## ERD

```mermaid
erDiagram
    users ||--o| lms_mentor_profiles : "volunteers as"
    lms_mentorships ||--o{ lms_mentorship_sessions : "logs"
    users ||--o{ lms_mentorships : "mentor/mentee"

    lms_mentor_profiles {
        ulid id PK
        ulid company_id
        ulid employee_id FK
        jsonb expertise
        string availability
        bool is_accepting
    }
    lms_mentorships {
        ulid id PK
        ulid company_id
        ulid mentor_id FK
        ulid mentee_id FK
        string focus_area
        jsonb goals
        string status
        timestamp started_at
        timestamp ended_at
    }
    lms_mentorship_sessions {
        ulid id PK
        ulid company_id
        ulid mentorship_id FK
        date session_date
        text notes
        jsonb action_items
        int rating
    }
```

`users`/employee owned by HR — shown for context.
