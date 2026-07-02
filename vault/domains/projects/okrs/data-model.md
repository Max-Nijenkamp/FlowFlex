---
domain: projects
module: okrs
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# OKRs — Data Model

## `proj_objectives`

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| description | text | nullable |
| owner_id | ulid FK users | |
| quarter | int 1–4 | |
| year | int | |
| parent_objective_id | ulid nullable FK self | cycle-checked, depth ≤ 4 *(assumed)* |
| project_id | ulid nullable FK | optional project link *(assumed)* |
| progress_percent | decimal(5,2) default 0 | computed cache |
| deleted_at | timestamp nullable | |

## `proj_key_results`

| Column | Type | Notes |
|---|---|---|
| id, objective_id FK, company_id | ulid | |
| title | string | |
| target_value / current_value | decimal(14,2) | |
| baseline_value | decimal(14,2) default 0 | start-value support |
| unit | string | %, €, count… |
| progress_percent | decimal(5,2) default 0 | |

## `proj_okr_checkins`
`id, key_result_id FK, company_id, user_id FK, current_value, notes, checked_in_at`.

## ERD

```mermaid
erDiagram
    proj_objectives ||--o{ proj_objectives : "parent (nested)"
    proj_objectives ||--o{ proj_key_results : has
    proj_key_results ||--o{ proj_okr_checkins : "progress updates"
    users ||--o{ proj_objectives : owns
    proj_objectives {
        ulid id PK
        ulid company_id
        string title
        ulid owner_id FK
        int quarter
        int year
        ulid parent_objective_id FK
        ulid project_id FK
        decimal progress_percent
    }
    proj_key_results {
        ulid id PK
        ulid objective_id FK
        ulid company_id
        decimal target_value
        decimal current_value
        decimal baseline_value
        string unit
        decimal progress_percent
    }
    proj_okr_checkins {
        ulid id PK
        ulid key_result_id FK
        ulid company_id
        ulid user_id FK
        decimal current_value
        text notes
        timestamp checked_in_at
    }
```
