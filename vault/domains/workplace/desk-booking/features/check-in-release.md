---
domain: workplace
module: desk-booking
feature: check-in-release
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Check-in & Auto-release

Claim a booked desk by checking in; auto-release unclaimed desks so walk-ins can use them.

## Behaviour

- `CheckInDeskAction` stamps `checked_in_at` on a `booked` desk (the employee arrives).
- `ReleaseDeskNoShowsCommand` (daily 11:00 *(assumed)*) sets `status = released` for today's `booked` desks with no check-in.
- Released desks become bookable again for the same day.

## UI

- **Kind**: custom-page action + background command
- **Page**: "Check in" action on the "my bookings" list / floor map detail; release runs as a scheduled command (no UI).
- **Layout**: a check-in button on today's booking; released desks show a "released — no-show" state on the map.
- **Key interactions**: click "Check in" → `checked_in_at` stamped, marker turns solid.
- **States**: empty (no bookings today) · loading (action) · error (already released / already checked-in toast) · selected (desk highlighted).
- **Gating**: `workplace.desks.book` (own booking).

## Data

- Owns / writes: `wp_desk_bookings` (`checked_in_at`, `status`) only.
- Reads: `wp_desks` (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: no-show / occupancy stats read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: none.

## Related

- [[../_module|Desk Booking]] · [[book-a-desk]] · [[../architecture]]
