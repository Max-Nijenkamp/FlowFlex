---
type: module
domain: Events Management
domain-key: events
panel: events
module-key: events.venues
status: planned
priority: p3
depends-on: [core.billing, core.rbac]
soft-depends: [events.events]
fires-events: []
consumes-events: []
patterns: [money]
tables: [ev_venues, ev_venue_rooms]
permission-prefix: events.venues
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Venues

Venue records for in-person events: location, capacity, rooms, and facilities. Reusable directory.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/events/events\|events.events]] | events reference venues; rooms feed session room picker |

---

## Core Features

- Venue record: name, address, capacity, contact, facilities
- Rooms within a venue (for multi-track events)
- Venue usage across events (which events used it)
- Map/directions link for attendees (address on landing page)
- Venue cost tracking
- Phone via `propaganistas/laravel-phone`

---

## Data Model

### ev_venues — id, company_id (indexed), name, address (jsonb), capacity, contact_name, contact_phone (E.164), facilities (jsonb), cost_cents nullable, deleted_at (blocked while referenced by upcoming events *(assumed)*)
### ev_venue_rooms — id, venue_id FK, company_id, name, capacity; unique `(venue_id, name)`

---

## DTOs

### CreateVenueData — name (required), address, capacity (min:1), contact_name?, contact_phone? (phone:AUTO), facilities[], cost_cents?, rooms[{name, capacity}]

## Services & Actions

None beyond CRUD — events reference `venue_id`; session room picker reads venue rooms.

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `VenueResource` | #1 CRUD resource | rooms relation, usage list |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('events.venues.view-any') && BillingService::hasModule('events.venues')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`events.venues.view-any` · `events.venues.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Delete blocked while upcoming events reference it
- [ ] Duplicate room name per venue rejected
- [ ] Phone normalised E.164

---

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

---

## Related

- [[domains/events/events]]
