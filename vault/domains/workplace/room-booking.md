---
type: module
domain: Workplace & Facility
domain-key: workplace
panel: workplace
module-key: workplace.rooms
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [wp_rooms, wp_room_bookings]
permission-prefix: workplace.rooms
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Room Booking

Book meeting rooms with availability calendar, recurring bookings, and conflict prevention. The Workplace anchor — build first in `/workplace`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | organisers/attendees are employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, booking confirmations |

---

## Core Features

- Room record: name, location/floor, capacity, amenities (projector, whiteboard, video)
- Availability calendar (saade/filament-fullcalendar)
- Book a room: date/time, organiser, attendees, purpose — `.ics` invite via icalendar-generator *(assumed)*
- Recurring bookings (daily/weekly, end date capped 6 months *(assumed)*) — occurrences materialised, conflicts skip+report
- Conflict prevention: no overlapping bookings per room (checked in transaction)
- Check-in / no-show release: free the room 15 min after start without check-in *(assumed)*
- Room utilisation reporting (analytics module)
- Filter rooms by capacity/amenities

---

## Data Model

### wp_rooms — id, company_id (indexed), name (unique per company), location, capacity, amenities (jsonb), is_bookable, deleted_at
### wp_room_bookings

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), room_id FK | ulid | |
| organiser_id | ulid FK users | |
| title | string | |
| start_at / end_at | timestamp | end after start; overlap-checked per room |
| status | string default `confirmed` | confirmed / cancelled / released |
| recurrence_group | ulid nullable | links occurrences |
| checked_in_at | timestamp nullable | |

**Indexes:** `(company_id, room_id, start_at, end_at)`

---

## DTOs

### BookRoomData — room_id (bookable), title, start_at/end_at (future, end after start, ≤ 8h *(assumed)*), attendee_ids[], recurrence {freq, until}? — conflict → `RoomUnavailableException` ("Room is already booked for this time.")

## Services & Actions

- `RoomBookingService::book(BookRoomData)` — transaction + overlap check; recurrence materialises occurrences (conflicting ones skipped + reported)
- `CheckInAction` / `CancelBookingAction`
- `ReleaseNoShowsCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ReleaseNoShowsCommand` | default | every 5 min | status guard confirmed + start+15m + no check-in |

---

## Filament

**Nav group:** Meeting Rooms

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `RoomResource` | #1 CRUD resource | amenities, bookable toggle |
| `RoomBookingPage` | #4 calendar custom page | fullcalendar, room filter, booking form; polling 30s |

---

## Permissions

`workplace.rooms.book` (all users) · `workplace.rooms.manage` · `workplace.rooms.cancel-any`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Overlap rejected incl. concurrent attempts (transaction)
- [ ] Recurrence materialises; conflicting occurrences skipped + reported
- [ ] No-show release at start+15m, once
- [ ] Cancel frees the slot
- [ ] Capacity/amenity filters

---

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

---

## Related

- [[domains/workplace/desk-booking]]
- [[architecture/packages]] (`saade/filament-fullcalendar`, `spatie/icalendar-generator`)
