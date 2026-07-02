---
domain: crm
module: sales-sequences
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sales Sequences — Data Model

## crm_sequences

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed; tenant scope |
| name | string | |
| owner_id | ulid | FK; personal owner (null = team sequence) |
| trigger_type | string | `manual` / `stage-change` / `segment-entry` / `deal-won` / `invoice-paid` |
| trigger_config | jsonb | Nullable; stage id / segment id |
| is_active | bool | |
| deleted_at | timestamp | Nullable (soft delete) |

## crm_sequence_steps

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| sequence_id | ulid | FK → crm_sequences |
| company_id | ulid | Tenant scope |
| order | int | Unique `(sequence_id, order)` |
| type | string | `email` / `call` / `wait` / `task` |
| config | jsonb | Template id(s) / variants, task text |
| wait_days | int | Default 0 |

Indexes: unique `(sequence_id, order)`.

## crm_sequence_enrolments

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| sequence_id | ulid | FK → crm_sequences |
| company_id | ulid | Indexed; tenant scope |
| contact_id | ulid | FK; unique active `(sequence_id, contact_id)` |
| deal_id | ulid | Nullable FK → crm_deals |
| current_step | int | Default 0 |
| status | string | Default `active` (active/paused/completed/unenrolled) |
| next_step_at | timestamp | Advancement cursor |
| variant_map | jsonb | A/B assignments |
| enrolled_at | timestamp | |

Indexes: `(company_id, status, next_step_at)` for the advance query; unique active `(sequence_id, contact_id)`.

## ERD

```mermaid
erDiagram
    crm_sequences {
        ulid id PK
        ulid company_id
        string name
        ulid owner_id FK
        string trigger_type
        jsonb trigger_config
        bool is_active
        timestamp deleted_at
    }
    crm_sequence_steps {
        ulid id PK
        ulid sequence_id FK
        ulid company_id
        int order
        string type
        jsonb config
        int wait_days
    }
    crm_sequence_enrolments {
        ulid id PK
        ulid sequence_id FK
        ulid company_id
        ulid contact_id FK
        ulid deal_id FK
        int current_step
        string status
        timestamp next_step_at
        jsonb variant_map
        timestamp enrolled_at
    }
    crm_contacts {
        ulid id PK
        ulid company_id
    }
    crm_deals {
        ulid id PK
        ulid company_id
    }
    crm_sequences ||--o{ crm_sequence_steps : "has steps"
    crm_sequences ||--o{ crm_sequence_enrolments : "has enrolments"
    crm_contacts ||--o{ crm_sequence_enrolments : "enrolled as"
    crm_deals ||--o{ crm_sequence_enrolments : "context for"
```
