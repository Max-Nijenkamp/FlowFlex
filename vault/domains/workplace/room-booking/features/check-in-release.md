---
domain: workplace
module: room-booking
feature: check-in-release
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Check-in & No-show Release

Confirm attendance to hold a booked room; auto-release the room when nobody checks in.

## Behaviour

- `CheckInAction` stamps `checked_in_at` on a `confirmed` booking (attendee arrives).
- `ReleaseNoShowsCommand` (every 5 min) sets `status = released` for `confirmed` bookings past `start_at + 15m` with no `checked_in_at` *(assumed cutoff)*.
- `CancelBookingAction` frees a slot manually (`status = cancelled`).
- Released/cancelled slots return to the available pool immediately.

## UI

- **Kind**: custom-page action (on the booking calendar) + background command
- **Page**: check-in / cancel are row/detail actions on `RoomBookingPage`; release is a scheduled command (no UI).
- **Layout**: booking detail slide-over shows a "Check in" button (near start time) and "Cancel"; released bookings render greyed with a "released — no-show" label.
- **Key interactions**: click "Check in" → `checked_in_at` stamped, block turns solid; cancel → confirm → slot freed.
- **States**: empty (n/a) · loading (action pending) · error (already-past / already-checked-in toast) · selected (booking highlighted).
- **Gating**: check-in/cancel own booking = `workplace.rooms.book`; cancel any = `workplace.rooms.cancel-any`.

## Data

- Owns / writes: `wp_room_bookings` (`checked_in_at`, `status`) only.
- Reads: `wp_rooms` (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: no-show rate read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Release predicate: `confirmed` + `now > start_at + 15m` + no `checked_in_at` *(assumed cutoff)*.

### Feature (Pest)
- [ ] Check-in stamps `checked_in_at`; a checked-in booking is never auto-released.
- [ ] `ReleaseNoShowsCommand` releases only past-cutoff no-shows, once (idempotent re-run makes no change).
- [ ] Cancel sets `status = cancelled` and returns the slot to the available pool.

### Livewire
- [ ] "Check in" action gated to the owner (`workplace.rooms.book`); cancel-any requires `workplace.rooms.cancel-any`.
- [ ] Already-checked-in / already-past shows the correct toast, no double stamp.

## Related

- [[../_module|Room Booking]] · [[book-a-room]] · [[../architecture]]
