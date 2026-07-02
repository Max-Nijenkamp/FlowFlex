---
domain: customer-success
module: playbooks
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Playbooks — Data Model

## cs_playbooks

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| name | string | not null | |
| trigger_type | string | not null | manual / health-drop / renewal / new-customer |
| trigger_config | jsonb | default `{}` | per-type config (e.g. renewal window days, health tier) |
| is_active | boolean | default true | |
| deleted_at | timestamp | nullable | soft delete |

## cs_playbook_steps

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, playbook_id (FK), company_id | ulid | | |
| title | string | not null | |
| description | text | nullable | |
| owner_role | string | not null | csm / manager |
| day_offset | int | default 0 | due = run start + offset days |
| order | int | not null | step sequence |

## cs_playbook_runs

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| playbook_id | ulid | not null FK | |
| account_id | ulid | not null FK crm_accounts | read-only ref |
| status | string | not null | active / completed / cancelled |
| started_at | timestamp | not null | |
| completed_at | timestamp | nullable | |

**Constraint:** partial unique `(company_id, playbook_id, account_id) WHERE status = 'active'` — one active run per (playbook, account).

## cs_playbook_run_steps

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, run_id (FK), step_id (FK), company_id | ulid | | |
| status | string | not null | open / done / skipped |
| due_date | date | not null | started_at + step.day_offset |
| assignee_id | ulid | nullable | resolved from owner_role (CSM = account owner) |
| completed_at | timestamp | nullable | |
| reminded | boolean | default false | due-reminder guard |

---

## ERD

```mermaid
erDiagram
    cs_playbooks ||--o{ cs_playbook_steps : "ordered steps"
    cs_playbooks ||--o{ cs_playbook_runs : "instances"
    cs_playbook_runs ||--o{ cs_playbook_run_steps : "materialised steps"
    cs_playbook_steps ||--o{ cs_playbook_run_steps : "template for"
    cs_playbooks {
        ulid id PK
        string name
        string trigger_type
        jsonb trigger_config
        boolean is_active
    }
    cs_playbook_steps {
        ulid id PK
        ulid playbook_id FK
        string owner_role
        int day_offset
        int order
    }
    cs_playbook_runs {
        ulid id PK
        ulid playbook_id FK
        ulid account_id FK
        string status
        timestamp started_at
    }
    cs_playbook_run_steps {
        ulid id PK
        ulid run_id FK
        ulid step_id FK
        string status
        date due_date
        ulid assignee_id
        boolean reminded
    }
```

`account_id` and `assignee_id` reference `crm_accounts` / user records (owner) as read-only foreign keys — this module never writes CRM tables.
