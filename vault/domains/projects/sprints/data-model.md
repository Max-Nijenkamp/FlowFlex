---
domain: projects
module: sprints
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Sprints — Data Model

## `proj_sprints`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed), project_id FK | ulid | | |
| name | string | not null | |
| goal | text | nullable | |
| start_date / end_date | date | end after start | |
| status | string | default `planning` | state machine |
| retro | jsonb | nullable | {went_well, improve, actions[]} |
| deleted_at | timestamp | nullable | SoftDeletes |

**Indexes:** `(company_id, project_id, status)` — one active enforced in service.

## `proj_sprint_tasks`

| Column | Type | Notes |
|---|---|---|
| id, sprint_id FK, task_id FK, company_id | ulid | unique `(sprint_id, task_id)`; task in one active sprint max |
| story_points | int nullable | |

## ERD

```mermaid
erDiagram
    proj_projects ||--o{ proj_sprints : has
    proj_sprints ||--o{ proj_sprint_tasks : contains
    proj_tasks ||--o{ proj_sprint_tasks : "assigned to sprint"
    proj_sprints {
        ulid id PK
        ulid company_id
        ulid project_id FK
        string name
        text goal
        date start_date
        date end_date
        string status
        jsonb retro
        timestamp deleted_at
    }
    proj_sprint_tasks {
        ulid id PK
        ulid sprint_id FK
        ulid task_id FK
        ulid company_id
        int story_points
    }
```
