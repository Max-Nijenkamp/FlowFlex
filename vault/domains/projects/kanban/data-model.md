---
domain: projects
module: kanban
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Kanban — Data Model

**Owns no tables.** Kanban is a pure view over projects.tasks.

## Reads (owned elsewhere)

| Table | Owner | Used for |
|---|---|---|
| `proj_tasks` | projects.tasks | cards |
| `proj_task_sections` | projects.tasks | columns (section-group mode) |

## Read model (output DTO)

`BoardData` — `columns[]` (id, name, task_count) + `cards[]` (task summary: id, title, assignee, priority, due_date, labels[], subtask_count). Built from a single query in `KanbanService`.

## ERD

```mermaid
erDiagram
    proj_task_sections ||--o{ proj_tasks : "column groups cards"
    proj_tasks {
        ulid id PK
        ulid section_id FK
        string status
        int order
    }
```

> No `proj_kanban_*` tables exist. Any board configuration (default group-by, saved filters) would be a future addition — see [[unknowns]].
