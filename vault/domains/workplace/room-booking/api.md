---
domain: workplace
module: room-booking
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Room Booking — API / DTOs

## `BookRoomData` (input)

| Field | Type | Rules |
|---|---|---|
| `room_id` | ulid | required, exists, `is_bookable = true` |
| `title` | string | required |
| `start_at` | timestamp | required, future |
| `end_at` | timestamp | required, after `start_at`, ≤ 8h span *(assumed)* |
| `attendee_ids` | ulid[] | nullable, employees in company |
| `recurrence` | object nullable | `{ freq: daily\|weekly, until: date }`, `until` ≤ 6 months *(assumed)* |

- Conflict → `RoomUnavailableException` ("Room is already booked for this time.").
- Recurrence: conflicting occurrences are skipped and reported back, not fatal.

## `RoomBookingService::book(BookRoomData): RoomBooking`

- Returns the created booking (or the first of a recurrence group).
- Side effects: confirmation notification, `.ics` invite *(assumed)*.

## Public / Portal Endpoints

None. Room booking is an internal `/workplace` panel surface with no public or portal-facing routes.
