---
type: module
domain: Events Management
panel: events
module-key: events.events
status: planned
color: "#4ADE80"
---

# Events

Create and manage events: details, schedule, capacity, venue, and status. The core entity of the Events domain.

## Core Features

- Event record: name, slug, description, type (in-person/virtual/hybrid), start/end, venue, capacity, status
- Status machine: `draft → published → live → completed | cancelled`
- Event types: conference, webinar, workshop, networking
- Capacity management with waitlist
- Event landing page (public, Vue + Inertia)
- Featured image + gallery
- Multi-session events (agenda with sessions)
- Virtual event links (Zoom/Teams/custom)
- Slug via `spatie/laravel-sluggable`

## Data Model

| Table | Key Columns |
|---|---|
| `ev_events` | company_id, name, slug, description, type, venue_id, start_at, end_at, capacity, status, virtual_link |
| `ev_sessions` | event_id, company_id, title, speaker_id, start_at, end_at, room |

## Filament

**Nav group:** Events

- `EventResource` — create, edit, publish, view
- `EventCalendarPage` (custom page) — calendar of events (saade/filament-fullcalendar)
- Agenda/sessions as relation manager

## Public Frontend

- Event landing + registration page (Vue + Inertia)

## Related

- [[domains/events/registrations]]
- [[domains/events/speakers]]
- [[domains/events/venues]]
