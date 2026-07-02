---
domain: workplace
module: room-booking
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Room Booking

Book meeting rooms with an availability calendar, recurring bookings, and conflict prevention. The Workplace anchor — build first in `/workplace`.

## Module-key

`workplace.rooms`

**Priority:** p3  
**Panel:** workplace  
**Permission prefix:** `workplace.rooms`  
**Tables:** `wp_rooms`, `wp_room_bookings`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | organisers/attendees are employees |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../core/notifications/_module\|core.notifications]] | booking confirmations |

## Core Features

- **Room catalogue** — name, location/floor, capacity, amenities (projector, whiteboard, video). See [[features/room-catalogue|Room Catalogue]].
- **Availability calendar + booking** — fullcalendar view, book date/time with organiser + attendees + purpose, `.ics` invite *(assumed)*. See [[features/book-a-room|Book a Room]].
- **Recurring bookings** — daily/weekly, capped 6 months *(assumed)*; occurrences materialised, conflicts skip + report. See [[features/recurring-bookings|Recurring Bookings]].
- **Conflict prevention** — no overlapping bookings per room, checked in a transaction (part of Book a Room).
- **Check-in / no-show release** — free the room 15 min after start without check-in *(assumed)*. See [[features/check-in-release|Check-in & No-show Release]].

## See features/

- [[features/room-catalogue|Room Catalogue]] · [[features/book-a-room|Book a Room]] · [[features/recurring-bookings|Recurring Bookings]] · [[features/check-in-release|Check-in & No-show Release]]

## Build Manifest

```
database/migrations/xxxx_create_wp_rooms_table.php
database/migrations/xxxx_create_wp_room_bookings_table.php
app/Models/Workplace/{Room,RoomBooking}.php
app/Data/Workplace/BookRoomData.php
app/Services/Workplace/RoomBookingService.php
app/Exceptions/Workplace/RoomUnavailableException.php
app/Actions/Workplace/{CheckInAction,CancelBookingAction}.php
app/Console/Commands/Workplace/ReleaseNoShowsCommand.php
app/Providers/Workplace/WorkplaceServiceProvider.php
app/Filament/Workplace/Resources/RoomResource.php
app/Filament/Workplace/Pages/RoomBookingPage.php
database/factories/Workplace/{RoomFactory,RoomBookingFactory}.php
tests/Feature/Workplace/{RoomBookingTest,RoomConflictTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see, book, or cancel company B's rooms/bookings
- [ ] Module gating: `RoomResource` + `RoomBookingPage` hidden when `workplace.rooms` inactive
- [ ] Overlap rejected incl. concurrent attempts (pessimistic transaction — see [[architecture#Concurrency]]).
- [ ] Recurrence materialises; conflicting occurrences skipped + reported.
- [ ] No-show release at start+15m, once.
- [ ] Cancel frees the slot.
- [ ] Capacity/amenity filters.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none confirmed)* | — | No cross-domain event specified *(assumed)*. A `RoomBooked` event could feed comms/calendar — undecided; see [[unknowns]]. |
| Reads | employee directory | hr.profiles | organiser + attendees resolved from HR employees (read-only) |
| Reads | invite generation | `spatie/icalendar-generator` | `.ics` attendee invites *(assumed)* |

**Data ownership:** `workplace.rooms` writes only `wp_rooms` + `wp_room_bookings`. Attendees/organisers are read from `hr.profiles` (read-only); confirmations are dispatched through `core.notifications`. No other domain's tables are written ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../desk-booking/_module|Desk Booking]] · [[../workplace-analytics/_module|Workplace Analytics]]
- [[../../../architecture/packages]] (`saade/filament-fullcalendar`, `spatie/icalendar-generator`)
