---
domain: projects
module: projects
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Projects — Data Model

## `proj_projects`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | `BelongsToCompany` |
| name | string | not null | |
| description | text | nullable | |
| status | string | default `planning` | state machine |
| start_date / target_date | date | target ≥ start | |
| completed_at | timestamp | nullable | |
| owner_id | ulid | not null FK users | |
| client_account_id | ulid | nullable | CRM link (read-only resolve) |
| estimated_minutes | int | nullable | |
| estimated_cost_cents | bigint | nullable | minor currency unit |
| color | string(7) | default per palette *(assumed)* | board/gantt display |
| deleted_at | timestamp | nullable | SoftDeletes |

**Indexes:** `(company_id, status)`, `(company_id, owner_id)`.

## `proj_project_members`

| Column | Type | Notes |
|---|---|---|
| id, project_id FK, company_id, user_id FK | ulid | unique `(project_id, user_id)` |
| role | string | owner / member / viewer |

## ERD

```mermaid
erDiagram
    proj_projects ||--o{ proj_project_members : has
    users ||--o{ proj_project_members : "member of"
    users ||--o{ proj_projects : owns
    crm_accounts |o--o{ proj_projects : "client (read-only)"

    proj_projects {
        ulid id PK
        ulid company_id
        string name
        string status
        date start_date
        date target_date
        timestamp completed_at
        ulid owner_id FK
        ulid client_account_id FK
        int estimated_minutes
        bigint estimated_cost_cents
        string color
        timestamp deleted_at
    }
    proj_project_members {
        ulid id PK
        ulid project_id FK
        ulid company_id
        ulid user_id FK
        string role
    }
```
