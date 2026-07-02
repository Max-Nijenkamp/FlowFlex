---
domain: events
module: events
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Events — API / DTOs

## `CreateEventData` (spatie/laravel-data)

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `description` | text | nullable; HTMLPurifier-sanitized |
| `type` | enum | required; in: in-person, virtual, hybrid |
| `venue_id` | ulid | required if type in-person/hybrid; exists in company |
| `start_at` | timestamp | required; future |
| `end_at` | timestamp | required; after `start_at` |
| `capacity` | int | nullable; min:1 |
| `virtual_link` | string | required if type virtual/hybrid; url |

## Read API (consumed by sibling modules)

- `EventService` exposes read access for published events + their sessions and venue.
- Sibling modules (registrations, speakers, sponsors, analytics) read event data through this service, never by querying `ev_events` directly across a bounded context boundary in a write path.

## Public / Portal Endpoints

| Route | Method | Auth | Purpose |
|---|---|---|---|
| `/e/{company}/{slug}` | GET | guest | Public event landing (published+ only). See [[features/public-landing]]. |

Draft/unpublished events return 404 on the public route.
