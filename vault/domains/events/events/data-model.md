---
domain: events
module: events
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Events — Data Model

## `ev_events`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string | |
| `slug` | string | Unique per company (`spatie/laravel-sluggable`) |
| `description` | text | HTMLPurifier-sanitized |
| `type` | string | in-person / virtual / hybrid |
| `venue_id` | ulid nullable | FK → `ev_venues` (in-person/hybrid) |
| `start_at` | timestamp | |
| `end_at` | timestamp | Must be after `start_at` |
| `capacity` | int nullable | null = unlimited |
| `status` | string | default `draft`; state machine |
| `virtual_link` | string nullable | Revealed to confirmed registrants only |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Indexes:** `(company_id, status)`, `(company_id, slug)` unique.

## `ev_sessions`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `event_id` | ulid | FK → `ev_events` |
| `title` | string | |
| `start_at` / `end_at` | timestamp | Must fall within the event window |
| `room` | string nullable | Drawn from the venue's rooms *(assumed: free-text or `ev_venue_rooms.name`)* |
| `order` | int | Agenda display order |

## ERD

```mermaid
erDiagram
    ev_events ||--o{ ev_sessions : "has agenda"
    ev_venues ||--o{ ev_events : "hosts"
    ev_venue_rooms ||--o{ ev_sessions : "room"

    ev_events {
        ulid id PK
        ulid company_id
        string name
        string slug
        string type
        ulid venue_id FK
        timestamp start_at
        timestamp end_at
        int capacity
        string status
        string virtual_link
        timestamp deleted_at
    }
    ev_sessions {
        ulid id PK
        ulid company_id
        ulid event_id FK
        string title
        timestamp start_at
        timestamp end_at
        string room
        int order
    }
    ev_venues {
        ulid id PK
        ulid company_id
    }
    ev_venue_rooms {
        ulid id PK
        string name
    }
```

> Note: `ev_venues` / `ev_venue_rooms` are owned by [[../venues/_module|events.venues]] and shown here only for the FK relationship.
