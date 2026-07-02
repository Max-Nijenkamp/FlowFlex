---
domain: lms
module: certifications
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Certifications — Data Model

## `lms_certificate_templates`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `name` | string | |
| `design` | jsonb | Logo, text, layout |
| `course_id` | ulid nullable | FK → `lms_courses` (optional association) |
| `validity_months` | int nullable | null = no expiry |

## `lms_certificates`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `learner_type` | string | employee / external |
| `learner_id` | ulid | |
| `course_id` | ulid | FK → `lms_courses` |
| `certificate_number` | string | Globally unique `FF-{ulid26}` *(assumed)* |
| `issued_at` | timestamp | |
| `expires_at` | timestamp nullable | From template validity |
| `alerted_levels` | jsonb | 60/14 reminder guards (default `[]`) |
| `pdf_path` | string nullable | Generated PDF |

## ERD

```mermaid
erDiagram
    lms_certificate_templates ||--o{ lms_certificates : "issued from"
    lms_courses ||--o| lms_certificate_templates : "may use"
    lms_courses ||--o{ lms_certificates : "certifies"
    lms_enrolments ||--o| lms_certificates : "completion issues"

    lms_certificate_templates {
        ulid id PK
        ulid company_id
        string name
        jsonb design
        ulid course_id FK
        int validity_months
    }
    lms_certificates {
        ulid id PK
        ulid company_id
        string learner_type
        ulid learner_id
        ulid course_id FK
        string certificate_number
        timestamp issued_at
        timestamp expires_at
        jsonb alerted_levels
        string pdf_path
    }
```

`lms_courses` / `lms_enrolments` owned by sibling modules — shown for context.
