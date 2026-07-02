---
domain: projects
module: tasks
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Tasks — Data Model

## `proj_tasks`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed), project_id FK | ulid | | |
| parent_task_id | ulid | nullable FK self | sub-tasks (unlimited nesting) |
| section_id | ulid | nullable FK | |
| title | string | not null | |
| description | text | nullable | |
| status | string | default `todo` | state machine |
| priority | string | default `medium` | urgent/high/medium/low |
| assignee_id | ulid | nullable FK users | project member |
| due_date | date | nullable | |
| estimated_hours | decimal(6,2) | nullable | |
| order | int | default 0 | board/section order |
| completed_at | timestamp | nullable | *(assumed)* |
| deleted_at | timestamp | nullable | SoftDeletes |

**Indexes:** `(company_id, project_id, status)`, `(company_id, assignee_id, status, due_date)` (My Tasks / workload).

## `proj_task_sections`
`id, project_id FK, company_id, name, order`.

## `proj_task_dependencies`
`id, task_id FK, depends_on_task_id FK, company_id, type (blocks/related)`; unique `(task_id, depends_on_task_id)`; cycle-checked in service.

## `proj_task_comments`
`id, task_id FK, company_id, user_id FK, body (purified), parent_comment_id nullable, deleted_at`.

## ERD

```mermaid
erDiagram
    proj_projects ||--o{ proj_tasks : contains
    proj_projects ||--o{ proj_task_sections : has
    proj_task_sections ||--o{ proj_tasks : groups
    proj_tasks ||--o{ proj_tasks : "sub-task (parent_task_id)"
    proj_tasks ||--o{ proj_task_dependencies : "blocks"
    proj_tasks ||--o{ proj_task_comments : "discussed in"
    users ||--o{ proj_tasks : assigned
    users ||--o{ proj_task_comments : authors

    proj_tasks {
        ulid id PK
        ulid company_id
        ulid project_id FK
        ulid parent_task_id FK
        ulid section_id FK
        string title
        string status
        string priority
        ulid assignee_id FK
        date due_date
        decimal estimated_hours
        int order
        timestamp completed_at
        timestamp deleted_at
    }
    proj_task_dependencies {
        ulid id PK
        ulid task_id FK
        ulid depends_on_task_id FK
        ulid company_id
        string type
    }
    proj_task_comments {
        ulid id PK
        ulid task_id FK
        ulid company_id
        ulid user_id FK
        text body
        ulid parent_comment_id FK
    }
```
