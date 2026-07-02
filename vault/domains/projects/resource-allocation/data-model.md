---
domain: projects
module: resource-allocation
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Resource Allocation — Data Model

## `proj_resource_allocations`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| user_id | ulid | not null FK | |
| project_id | ulid | not null FK | |
| allocation_percent | int | 1–100 | |
| start_date / end_date | date | end ≥ start | |
| deleted_at | timestamp | nullable | SoftDeletes |

**Indexes:** `(company_id, user_id, start_date, end_date)`.

## ERD

```mermaid
erDiagram
    proj_projects ||--o{ proj_resource_allocations : "staffed by"
    users ||--o{ proj_resource_allocations : "allocated to"
    proj_resource_allocations {
        ulid id PK
        ulid company_id
        ulid user_id FK
        ulid project_id FK
        int allocation_percent
        date start_date
        date end_date
        timestamp deleted_at
    }
```
