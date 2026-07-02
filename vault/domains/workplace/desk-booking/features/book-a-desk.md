---
domain: workplace
module: desk-booking
feature: book-a-desk
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Book a Desk

Reserve a desk for a date from the floor map, enforcing dual-uniqueness and booking-window rules.

## Behaviour

1. From the floor map, user picks a date and clicks a free desk.
2. `DeskBookingService::book` validates: desk bookable + free that date; date future + ≤ max advance; ≤ max consecutive.
3. Transaction asserts both uniqueness rules — `(desk_id, booking_date)` and `(employee_id, booking_date)`.
4. On success the desk is stamped `booked`; recurrence expands to weekday rows until an end date *(assumed)*.
5. Rule violations return friendly messages ("You already have a desk booked for this date.").

## UI

- **Kind**: custom-page action (book modal on the floor map)
- **Page**: book modal on `DeskBookingPage`.
- **Layout**: modal shows desk identifier, zone, equipment, date, optional recurrence (weekdays + until).
- **Key interactions**: click free desk → confirm modal → optimistic marker flip to "mine"; polling reconciles.
- **States**: empty (n/a) · loading (booking) · error (rule violation toast) · selected (desk marker highlighted, modal open).
- **Gating**: `workplace.desks.book`.

## Data

- Owns / writes: `wp_desk_bookings` only.
- Reads: `wp_desks` (own module); `hr.profiles` to resolve the acting employee (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: bookings read by [[../../workplace-analytics/_module|Workplace Analytics]] (occupancy).
- Shared entity: `hr_employees` — owned by [[../../../hr/employee-profiles/_module|hr.profiles]], read-only.

## Related

- [[../_module|Desk Booking]] · [[floor-map]] · [[check-in-release]] · [[../api]]
