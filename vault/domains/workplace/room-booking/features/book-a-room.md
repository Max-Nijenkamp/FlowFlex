---
domain: workplace
module: room-booking
feature: book-a-room
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Book a Room

Reserve a meeting room in a chosen time slot from the availability calendar, with conflict prevention.

## Behaviour

1. User picks a room + slot on the calendar (or via a "new booking" form).
2. `RoomBookingService::book` validates the window (bookable room, `end_at` after `start_at`, ≤ 8h *(assumed)*).
3. Overlap check on `(room_id, start_at, end_at)` inside a transaction; conflict → `RoomUnavailableException`.
4. On success: booking stamped `confirmed`, confirmation notification sent, `.ics` invite to attendees *(assumed)*.
5. Filter rooms by capacity / amenities before booking.

## UI

- **Kind**: custom-page (calendar)
- **Page**: `RoomBookingPage` — "Room Booking" (`/workplace/rooms/calendar`), `saade/filament-fullcalendar`.
- **Layout**: fullcalendar centre; left rail = room filter (capacity, amenities); booking form in a slide-over/modal on slot select.
- **Key interactions**: click/drag a slot → booking modal → confirm → optimistic calendar block; polling refresh 30s.
- **States**: empty (no rooms → link to catalogue) · loading (calendar skeleton) · error (conflict toast "Room is already booked for this time." + retry) · selected (slot highlighted, modal open).
- **Gating**: view `workplace.rooms.view-any`; book `workplace.rooms.book`.

## Data

- Owns / writes: `wp_room_bookings` only.
- Reads: `hr.profiles` for organiser/attendee resolution (read-only); `wp_rooms` (own module).
- Cross-domain writes: none — notifications dispatched via `core.notifications`, never by writing its tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: bookings read by [[../../workplace-analytics/_module|Workplace Analytics]]. A `RoomBooked` cross-domain event is *(assumed)* / undecided ([[../unknowns]]).
- Shared entity: `hr_employees` (organiser/attendees) — owned by [[../../../hr/employee-profiles/_module|hr.profiles]], read-only.

## Test Checklist

### Unit
- [ ] Window validation: `end_at` after `start_at`, duration ≤ 8h *(assumed)*, bookable room only.
- [ ] `BookRoomData` DTO rejects a non-bookable / archived room.

### Feature (Pest)
- [ ] Happy path: valid slot creates a `confirmed` booking + dispatches the confirmation notification.
- [ ] Overlap on `(room_id, start_at, end_at)` throws `RoomUnavailableException`, no row written.
- [ ] **Concurrent booking**: two simultaneous `book()` calls for the same room/slot — exactly one succeeds, the other gets `RoomUnavailableException` (pessimistic lock, see [[../architecture#Concurrency]]).
- [ ] Capacity / amenity filter narrows the bookable room set.

### Livewire
- [ ] Booking modal validates required fields; conflict surfaces the "Room is already booked for this time." toast + retry.
- [ ] `canAccess` false without `workplace.rooms.book`; book action hidden.

## Related

- [[../_module|Room Booking]] · [[recurring-bookings]] · [[check-in-release]] · [[../api]]
