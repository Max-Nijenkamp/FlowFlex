---
domain: workplace
module: maintenance
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Facility Maintenance — Data Model

## `wp_maintenance_requests`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `location` | string | |
| `category` | string | HVAC / electrical / plumbing / cleaning / furniture / safety |
| `description` | text | |
| `priority` | string | urgent / high / normal / low |
| `status` | string | state machine (default `reported`) |
| `reporter_id` | ulid | FK → `users` |
| `assignee_id` | ulid nullable | staff |
| `contractor` | string nullable | external (free-text *(assumed)*) |
| `schedule_id` | ulid nullable | preventive origin → `wp_maintenance_schedules` |
| `resolved_at` / `closed_at` | timestamp nullable | |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `wp_maintenance_schedules`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `location` | string | |
| `task` | string | |
| `category` | string | in set |
| `frequency` | string | weekly / monthly / quarterly |
| `next_due_at` | date | advanced on run |
| `is_active` | boolean | |

## ERD

```mermaid
erDiagram
    wp_maintenance_schedules ||--o{ wp_maintenance_requests : "auto-creates"
    users ||--o{ wp_maintenance_requests : reports

    wp_maintenance_requests {
        ulid id PK
        ulid company_id
        string location
        string category
        text description
        string priority
        string status
        ulid reporter_id FK
        ulid assignee_id
        string contractor
        ulid schedule_id FK
        timestamp resolved_at
        timestamp closed_at
        timestamp deleted_at
    }
    wp_maintenance_schedules {
        ulid id PK
        ulid company_id
        string location
        string task
        string category
        string frequency
        date next_due_at
        bool is_active
    }
```
