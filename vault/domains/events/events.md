---
type: module
domain: Events Management
domain-key: events
panel: events
module-key: events.events
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files]
soft-depends: [events.venues, events.speakers, events.registrations]
fires-events: []
consumes-events: []
patterns: [states, custom-pages]
tables: [ev_events, ev_sessions]
permission-prefix: events.events
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Events

Create and manage events: details, schedule, capacity, venue, and status. The core entity of the Events domain — build first in `/events`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, images |
| Soft | [[domains/events/venues\|events.venues]] (venue link), [[domains/events/speakers\|events.speakers]] (session speakers), [[domains/events/registrations\|events.registrations]] (attendees) | enrichments |

---

## Core Features

- Event record: name, slug, description, type (in-person/virtual/hybrid), start/end, venue, capacity, status
- Status machine: `draft → published → live → completed | cancelled`
- Event types: conference, webinar, workshop, networking
- Capacity management with waitlist (enforced by registrations module)
- Event landing page (public, Vue + Inertia) — published+ only
- Featured image + gallery (Media Library)
- Multi-session events (agenda with sessions, room from venue)
- Virtual event links (revealed to confirmed registrants only *(assumed)*)
- Slug via `spatie/laravel-sluggable`

---

## Data Model

### ev_events

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| slug | string | unique per company |
| description | text | purified |
| type | string | in-person/virtual/hybrid |
| venue_id | ulid nullable | |
| start_at / end_at | timestamp | end after start |
| capacity | int nullable | null = unlimited |
| status | string default `draft` | state machine |
| virtual_link | string nullable | confirmed-only reveal |
| deleted_at | timestamp nullable | |

### ev_sessions — id, event_id FK, company_id, title, start_at/end_at (within event window), room nullable, order

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `draft` | `published` | `events.events.publish` | landing page live, registration opens |
| `published` | `live` | start time (scheduled) or manual | |
| `live` | `completed` | end time or manual | |
| `draft`/`published` | `cancelled` | `events.events.cancel` | registrants notified (registrations module) |

---

## DTOs

### CreateEventData — name, description (purified), type (in set), venue_id? (in-person/hybrid), start_at/end_at (end after start, future), capacity?, virtual_link? (virtual/hybrid)

## Services & Actions

- `EventService::publish/cancel` — cancel cascades notification via registrations (direct same-domain call)
- `EventLifecycleCommand` — published→live→completed at times

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `EventLifecycleCommand` | default | every 15 min | status+time guards |

---

## Filament

**Nav group:** Events

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EventResource` | #1 CRUD resource | publish/cancel actions, sessions relation |
| `EventCalendarPage` | #4 calendar custom page | fullcalendar of events |

Public landing: Vue + Inertia `/e/{company}/{slug}` — ui-strategy row #16.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('events.events.view-any') && BillingService::hasModule('events.events')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rich-text sanitize** (medium): State that description is sanitized via HTMLPurifier before persistence.

---

## Permissions

`events.events.view-any` · `events.events.create` · `events.events.update` · `events.events.publish` · `events.events.cancel`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Draft landing 404; published renders
- [ ] Session times must fall within event window
- [ ] Lifecycle command transitions at times, once
- [ ] Cancel notifies registrants
- [ ] Virtual link hidden from unconfirmed/public

---

## Build Manifest

```
database/migrations/xxxx_create_ev_events_table.php
database/migrations/xxxx_create_ev_sessions_table.php
app/Models/Events/{Event,EventSession}.php
app/States/Events/Event/{EventState,Draft,Published,Live,Completed,Cancelled}.php
app/Data/Events/CreateEventData.php
app/Services/Events/EventService.php
app/Providers/Events/EventsServiceProvider.php
app/Console/Commands/Events/EventLifecycleCommand.php
app/Http/Controllers/PublicEventController.php + resources/js/Pages/Events/Landing.vue
app/Filament/Events/Resources/EventResource.php
app/Filament/Events/Pages/EventCalendarPage.php
database/factories/Events/{EventFactory,EventSessionFactory}.php
tests/Feature/Events/EventLifecycleTest.php
```

---

## Related

- [[domains/events/registrations]]
- [[domains/events/speakers]]
- [[domains/events/venues]]
