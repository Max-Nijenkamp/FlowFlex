---
domain: crm
module: appointment-scheduling
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Appointment Scheduling — Unknowns & Open Questions

## Assumptions

- *(assumed)* OAuth calendar sync (Google/Outlook, two-way) is a v1.x fast-follow, not v1.
- *(assumed)* Zoom/Meet API video link generation lands later; v1 uses a static link.
- *(assumed)* Reminder fires 24h before the meeting.
- *(assumed)* Round-robin assigns to the rep with the fewest bookings this week.
- *(assumed)* `owner_id` null on a meeting type means team round-robin.

## Open Questions

- Timezone handling: is `scheduled_at` stored UTC with the prospect's tz captured separately for display and `.ics`?
- Should reminders be configurable per meeting type (e.g. 24h + 1h) rather than a single fixed window?
- Does cancellation by the prospect require a signed link, and what is its expiry?
- For paid bookings, is a hold placed on the slot during PaymentIntent, and for how long before it releases?
