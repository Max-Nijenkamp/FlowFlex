---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.rooms
status: planned
color: "#4ADE80"
---

# Room Booking

Book meeting rooms with availability calendar, recurring bookings, and conflict prevention.

## Core Features

- Room record: name, location/floor, capacity, amenities (projector, whiteboard, video)
- Availability calendar (saade/filament-fullcalendar)
- Book a room: date/time, organiser, attendees, purpose
- Recurring bookings (daily/weekly)
- Conflict prevention: no double-booking
- Check-in / no-show release (free the room if organiser doesn't check in)
- Room utilisation reporting
- Filter rooms by capacity/amenities

## Data Model

| Table | Key Columns |
|---|---|
| `wp_rooms` | company_id, name, location, capacity, amenities (json), is_bookable |
| `wp_room_bookings` | company_id, room_id, organiser_id, title, start_at, end_at, status, recurrence (json), checked_in_at |

## Filament

**Nav group:** Meeting Rooms

- `RoomResource` — manage rooms
- `RoomBookingPage` (custom page) — calendar view + booking form (fullcalendar)

## Related

- [[domains/workplace/desk-booking]]
- `saade/filament-fullcalendar`
