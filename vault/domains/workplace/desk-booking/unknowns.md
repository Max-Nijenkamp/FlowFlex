---
domain: workplace
module: desk-booking
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Desk Booking — Unknowns

## Assumed Items

- Bookings are full-day for v1 (no half-day slots) *(assumed)*.
- Status is a plain string field, not a state-machine class *(assumed)*.
- Max advance 14 days / max consecutive 5 days *(assumed defaults, configurable)*.
- No-show cutoff 11:00 daily *(assumed)*.
- Floor map = positioned divs over an uploaded floor image *(assumed)*.
- Floor map polls 60s; no websocket *(assumed)*.

## Open Questions

- Should desk booking fire a `DeskBooked` event (e.g. for attendance / analytics streaming), or stay internal?
- Half-day / AM-PM slots for v2?
- Can an employee opt out of appearing in the team view (privacy)?
- Are zones bookable as neighbourhoods (team blocks), or desks only?
- Does check-in require a physical action (QR at desk) or any authenticated tap?
- Should a "favourite desk" / auto-rebook exist for regulars?
