---
domain: marketing
module: email-sequences
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Email Sequences — Data Model

Owns three tables, all company-scoped. Reuses campaign open/click tracking machinery per send.

### mkt_sequences

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| trigger_type | string | form / segment / contact-created / manual |
| trigger_config | jsonb | validated per type |
| is_active | boolean | pause flag |
| deleted_at | timestamp nullable | |

### mkt_sequence_steps

| Column | Type | Notes |
|---|---|---|
| id, sequence_id FK, company_id | ulid | |
| order | int | unique per sequence |
| email_subject | string | |
| email_body | text | purified |
| wait_days | int | delay before next step |

### mkt_sequence_enrolments

| Column | Type | Notes |
|---|---|---|
| id, sequence_id FK, company_id (indexed), contact_id FK | ulid | unique active `(sequence_id, contact_id)` |
| current_step | int default 0 | |
| status | string default `active` | active / paused / completed / exited |
| next_step_at | timestamp | advancement cursor |
| enrolled_at / completed_at | timestamp | |

**Indexes:** `(company_id, status, next_step_at)` — the advancement sweep predicate.

## ERD

```mermaid
erDiagram
    MKT_SEQUENCES ||--o{ MKT_SEQUENCE_STEPS : contains
    MKT_SEQUENCES ||--o{ MKT_SEQUENCE_ENROLMENTS : enrols
    MKT_SEQUENCES {
        ulid id PK
        ulid company_id
        string name
        string trigger_type
        jsonb trigger_config
        bool is_active
    }
    MKT_SEQUENCE_STEPS {
        ulid id PK
        ulid sequence_id FK
        int order
        string email_subject
        int wait_days
    }
    MKT_SEQUENCE_ENROLMENTS {
        ulid id PK
        ulid sequence_id FK
        ulid company_id
        ulid contact_id
        int current_step
        string status
        timestamp next_step_at
    }
```

## Related

- [[_module]] · [[architecture]] · [[../campaigns/data-model]] (shared `mkt_unsubscribes`)
