---
domain: workplace
module: desk-booking
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Desk Booking — API / DTOs

## `BookDeskData` (input)

| Field | Type | Rules |
|---|---|---|
| `desk_id` | ulid | required, exists, `is_bookable`, free that date |
| `booking_date` | date | required, future, ≤ max advance (default 14) |
| `recurrence` | object nullable | `{ weekdays: [mon..fri], until: date }`, ≤ max consecutive (default 5) *(assumed)* |

Rule violations return messages, e.g. "You already have a desk booked for this date." / "That desk is taken for this date."

## `DeskBookingService::book(BookDeskData): DeskBooking|Collection`

- Enforces both uniqueness rules + advance/consecutive limits inside a transaction.
- Returns the booking, or the collection of weekday occurrences for a recurrence.

## Public / Portal Endpoints

None. Desk booking is an internal `/workplace` panel surface.
