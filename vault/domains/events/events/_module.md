---
domain: events
module: events
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Events

Create and manage events — details, schedule, capacity, venue, and status. The anchor entity of the Events domain; every other module hangs off an event. Build first in `/events`.

## Module-key

| Field | Value |
|---|---|
| key | `events.events` |
| priority | p3 |
| panel | events |
| permission-prefix | `events.events` |
| tables | `ev_events`, `ev_sessions` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()` |
| Hard | [[../../core/file-storage/_module\|core.files]] | Featured image + gallery (Media Library) |
| Soft | [[../venues/_module\|events.venues]] | Venue link + session room picker |
| Soft | [[../speakers/_module\|events.speakers]] | Session speaker assignments |
| Soft | [[../registrations/_module\|events.registrations]] | Attendees per published event |

## Core Features

- **Event record** — name, slug, description, type (in-person/virtual/hybrid), start/end, venue, capacity, status.
- **Status lifecycle** — `draft → published → live → completed | cancelled` (spatie/model-states).
- **Multi-session agenda** — sessions within the event window, each with a room drawn from the venue.
- **Public landing page** — Vue + Inertia, published+ only, at `/e/{company}/{slug}`.
- **Featured image + gallery** — Media Library.
- **Virtual event link** — revealed to confirmed registrants only *(assumed)*.

## See features/

- [[features/event-crud|Event CRUD & Lifecycle]] — the event record + state machine + scheduled lifecycle command.
- [[features/agenda-sessions|Agenda & Sessions]] — multi-session schedule, rooms, agenda custom page.
- [[features/public-landing|Public Landing Page]] — Vue + Inertia public event page.

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's events data
- [ ] Module gating: artifacts hidden when `events.events` inactive
- [ ] Draft landing 404; published renders.
- [ ] Session times must fall within the event window.
- [ ] Lifecycle command transitions at times, once.
- [ ] Cancel notifies registrants.
- [ ] Virtual link hidden from unconfirmed/public.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | venue + rooms | events.venues | Event references `venue_id`; session room picker reads venue rooms |
| Feeds (same-domain) | `EventService::cancel` | events.registrations | Cancel triggers registrant notification (same-domain service call) |
| Feeds (read) | published event | events.registrations, events.speakers, events.sponsors, events.analytics | All hang off a published event |

**Data ownership:** `events.events` writes only `ev_events` + `ev_sessions`. Venue data is read through the Venues service; registrant notification on cancel is a same-domain call. No other domain's tables are ever written ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../venues/_module|Venues]] · [[../registrations/_module|Registrations]] · [[../speakers/_module|Speakers]] · [[../sponsors/_module|Sponsors]]
- [[../_index|Events MOC]]
