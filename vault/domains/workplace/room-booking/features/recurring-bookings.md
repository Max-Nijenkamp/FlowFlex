---
domain: workplace
module: room-booking
feature: recurring-bookings
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Recurring Bookings

Book a room on a repeating schedule (daily/weekly), materialised into individual occurrences.

## Behaviour

- Organiser sets `recurrence { freq: daily|weekly, until }`, capped 6 months *(assumed)*.
- `RoomBookingService::book` expands occurrences into individual `wp_room_bookings` rows linked by `recurrence_group`.
- Occurrences that conflict with an existing booking are **skipped and reported** (not fatal); non-conflicting ones are created.
- Each occurrence is independently cancellable and check-in-able.

## UI

- **Kind**: custom-page (extends the booking calendar modal)
- **Page**: recurrence controls inside the [[book-a-room|Book a Room]] modal on `RoomBookingPage`.
- **Layout**: recurrence sub-form (frequency, end date) below the slot fields; a post-submit summary lists created vs skipped occurrences.
- **Key interactions**: enable recurrence → pick freq + until → submit → summary of created/skipped.
- **States**: empty (n/a) · loading (materialising) · error (whole series invalid → validation) · partial (some occurrences skipped → info banner listing conflicts).
- **Gating**: `workplace.rooms.book`.

## Data

- Owns / writes: `wp_room_bookings` (multiple rows, one `recurrence_group`).
- Reads: `wp_rooms` (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: occurrences read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: none.

## Related

- [[../_module|Room Booking]] · [[book-a-room]] · [[../decisions]]
