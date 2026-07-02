---
domain: events
module: venues
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Venues — Data Model

## `ev_venues`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `name` | string | |
| `address` | jsonb | Structured address |
| `capacity` | int | |
| `contact_name` | string nullable | |
| `contact_phone` | string nullable | E.164 (`propaganistas/laravel-phone`) |
| `facilities` | jsonb | |
| `cost_cents` | bigint nullable | |
| `deleted_at` | timestamp nullable | `SoftDeletes`; blocked while referenced by upcoming events *(assumed)* |

## `ev_venue_rooms`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `venue_id` | ulid | FK → `ev_venues` |
| `name` | string | |
| `capacity` | int | |

**Indexes:** unique `(venue_id, name)`.

## ERD

```mermaid
erDiagram
    ev_venues ||--o{ ev_venue_rooms : "has"
    ev_venues ||--o{ ev_events : "hosts (read)"

    ev_venues {
        ulid id PK
        ulid company_id
        string name
        jsonb address
        int capacity
        string contact_phone
        jsonb facilities
        bigint cost_cents
        timestamp deleted_at
    }
    ev_venue_rooms {
        ulid id PK
        ulid venue_id FK
        string name
        int capacity
    }
    ev_events { ulid id PK
        ulid venue_id FK }
```

> `ev_events` is owned by [[../events/_module|events.events]]; shown for the reference only.
