---
domain: projects
module: milestones
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Milestones — Data Model

## `proj_milestones`

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), project_id FK | ulid | |
| title | string | |
| description | text | nullable |
| target_date | date | |
| achieved_date | date | nullable |
| status | string | default `open` — open / achieved / missed (plain enum *(assumed)*) |
| notes | text | nullable — achievement notes |
| reminded_at | timestamp | nullable — 7-day once-guard |
| deleted_at | timestamp | nullable |

**Indexes:** `(company_id, status, target_date)`.

## `proj_milestone_tasks`
`id, milestone_id FK, task_id FK, company_id`; unique `(milestone_id, task_id)`.

## ERD

```mermaid
erDiagram
    proj_projects ||--o{ proj_milestones : has
    proj_milestones ||--o{ proj_milestone_tasks : "progress from"
    proj_tasks ||--o{ proj_milestone_tasks : "linked to"
    proj_milestones {
        ulid id PK
        ulid company_id
        ulid project_id FK
        string title
        date target_date
        date achieved_date
        string status
        text notes
        timestamp reminded_at
        timestamp deleted_at
    }
    proj_milestone_tasks {
        ulid id PK
        ulid milestone_id FK
        ulid task_id FK
        ulid company_id
    }
```
