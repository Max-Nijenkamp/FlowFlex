---
domain: workplace
module: room-booking
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Room Booking — Unknowns

## Assumed Items

- Booking status is a plain string field, not a `spatie/laravel-model-states` machine *(assumed)*.
- No-show cutoff is `start_at + 15m` *(assumed default, configurable)*.
- Max booking span 8h; recurrence capped 6 months *(assumed defaults)*.
- `.ics` invites via `spatie/icalendar-generator` *(assumed — flow says "invite")*.
- Attendees are **not** persisted to a join table; they live only on the `.ics` invite *(assumed)*.
- Calendar page polls 30s; no websocket channel *(assumed)*.

## Open Questions

- Should booking a room fire a `RoomBooked` cross-domain event (comms / shared calendar), or stay internal?
- Persist attendees as `wp_room_booking_attendees` for reporting, or leave on the invite only?
- How are overlapping recurrence conflicts surfaced to the organiser — inline report, email digest, or both?
- Is the no-show cutoff per-company configurable, and does check-in require a physical device (room display / QR), or any authenticated action?
- Does capacity enforcement block over-capacity attendee lists, or warn only?
