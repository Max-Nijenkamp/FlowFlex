---
domain: projects
module: workload
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Workload — Data Model

**Owns no tables.** Workload is a pure view.

## Reads (owned elsewhere)

| Table / source | Owner | Used for |
|---|---|---|
| `proj_tasks` | projects.tasks | assignee, estimated_hours, due_date, status |
| capacity (working-time) | hr.profiles | per-user daily capacity (default 8h when inactive) |
| `proj_resource_allocations` | projects.resources | allocation overlay |

## Read model (output DTO)

`WorkloadGridData` — `rows[]` (user, capacity_hours, `cells[]` {date, hours, level}).

## ERD

```mermaid
erDiagram
    users ||--o{ proj_tasks : assigned
    proj_tasks {
        ulid id PK
        ulid assignee_id FK
        decimal estimated_hours
        date due_date
        string status
    }
```

> No `proj_workload_*` tables. Capacity source is HR when active, else an 8h/day default constant.
