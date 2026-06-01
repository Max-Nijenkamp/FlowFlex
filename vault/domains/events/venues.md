---
type: module
domain: Events Management
panel: events
module-key: events.venues
status: planned
color: "#4ADE80"
---

# Venues

Venue records for in-person events: location, capacity, rooms, and facilities.

## Core Features

- Venue record: name, address, capacity, contact, facilities
- Rooms within a venue (for multi-track events)
- Venue availability across events
- Map/directions for attendees
- Venue cost tracking
- Reusable venue directory

## Data Model

| Table | Key Columns |
|---|---|
| `ev_venues` | company_id, name, address, capacity, contact_name, contact_phone, facilities (json), cost_cents |
| `ev_venue_rooms` | venue_id, company_id, name, capacity |

## Filament

**Nav group:** Settings

- `VenueResource` — manage venues + rooms
- Selected on event creation

## Related

- [[domains/events/events]]
