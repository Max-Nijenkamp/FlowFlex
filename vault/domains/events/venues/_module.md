---
domain: events
module: venues
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Venues

A reusable venue directory for in-person events: location, capacity, rooms, facilities, and cost.

## Module-key

| Field | Value |
|---|---|
| key | `events.venues` |
| priority | p3 |
| panel | events |
| permission-prefix | `events.venues` |
| tables | `ev_venues`, `ev_venue_rooms` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Soft | [[../events/_module\|events.events]] | Events reference venues; rooms feed the session room picker |

## Core Features

- **Venue record** — name, address, capacity, contact, facilities, cost.
- **Rooms** within a venue (for multi-track events) — feed the session room picker.
- **Usage** — which events used a venue.
- **Map/directions** link on the landing page (address).
- **Phone** via `propaganistas/laravel-phone` (E.164).

## See features/

- [[features/venue-directory|Venue Directory]] — the reusable venue records.
- [[features/rooms|Rooms]] — rooms within a venue.

## Build Manifest

```
database/migrations/xxxx_create_ev_venues_table.php
database/migrations/xxxx_create_ev_venue_rooms_table.php
app/Models/Events/{Venue,VenueRoom}.php
app/Data/Events/CreateVenueData.php
app/Filament/Events/Resources/VenueResource.php
database/factories/Events/VenueFactory.php
tests/Feature/Events/VenueTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Delete blocked while upcoming events reference it.
- [ ] Duplicate room name per venue rejected.
- [ ] Phone normalised to E.164.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Feeds (read) | venue + rooms | events.events | Events read `venue_id`; sessions read venue rooms |

**Data ownership:** `events.venues` writes only `ev_venues` + `ev_venue_rooms`. Events reference venues by `venue_id` (read); venues never writes `ev_events`/`ev_sessions`. No cross-domain events ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../events/_module|Events]]
- [[../_index|Events MOC]]
