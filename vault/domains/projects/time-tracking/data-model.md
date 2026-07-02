---
domain: projects
module: time-tracking
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Time Tracking — Data Model

## `proj_time_entries`

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| task_id | ulid | nullable FK | project-level entries allowed *(assumed)* |
| project_id | ulid | not null FK | |
| user_id | ulid | not null FK | |
| description | string | nullable | |
| date | date | not null, not future | |
| minutes_logged | int | > 0 | **minutes int, not decimal hours** |
| is_billable | boolean | default false | |
| timer_started_at | timestamp | nullable | running-timer marker |
| approved_by / approved_at | ulid / timestamp | nullable | |
| deleted_at | timestamp | nullable | SoftDeletes |

**Indexes:** `(company_id, user_id, date)`, `(company_id, project_id, date)`; partial: one row per user where `timer_started_at` not null *(assumed — enforced in service)*.

## ERD

```mermaid
erDiagram
    proj_projects ||--o{ proj_time_entries : "logged against"
    proj_tasks |o--o{ proj_time_entries : "optional task"
    users ||--o{ proj_time_entries : logs
    proj_time_entries {
        ulid id PK
        ulid company_id
        ulid task_id FK
        ulid project_id FK
        ulid user_id FK
        string description
        date date
        int minutes_logged
        boolean is_billable
        timestamp timer_started_at
        ulid approved_by FK
        timestamp approved_at
        timestamp deleted_at
    }
```
