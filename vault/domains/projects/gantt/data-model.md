---
domain: projects
module: gantt
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Gantt — Data Model

**Owns no tables.** Gantt is a pure view.

## Reads (owned elsewhere)

| Table | Owner | Used for |
|---|---|---|
| `proj_tasks` | projects.tasks | bars (start→due), progress |
| `proj_task_dependencies` | projects.tasks | dependency arrows + critical path |
| `proj_milestones` | projects.milestones | markers |

## Read model (output DTO)

`GanttData` — `tasks[]` (id, title, start, end, progress, dependencies[]), `milestones[]` (id, title, date), `critical_path_ids[]`.

## ERD

```mermaid
erDiagram
    proj_tasks ||--o{ proj_task_dependencies : "arrow"
    proj_projects ||--o{ proj_tasks : bars
    proj_projects ||--o{ proj_milestones : markers
    proj_tasks {
        ulid id PK
        date due_date
        integer estimated_minutes (minutes, int — unit decision 2026-07-03)
    }
    proj_milestones {
        ulid id PK
        date target_date
    }
```
