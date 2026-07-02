---
domain: events
module: events
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Events — Unknowns

## Assumed Items

- Virtual link reveal is confirmed-registrant-only *(assumed)* — mechanism/route not specified.
- Session `room` is either free-text or references `ev_venue_rooms.name` *(assumed)* — the FK vs. string choice is unspecified.
- Custom registration questions are stored on the event as a jsonb definition (`events.custom_questions`) *(assumed)* — see the registrations module.

## Open Questions

- Where exactly is the confirmed-only `virtual_link` surfaced — the confirmation email, a portal page, or both?
- Should `EventLifecycleCommand` fire a domain event on `published → live` / `live → completed` (e.g. for reminders/analytics), or stay side-effect-free?
- Recurring / series events — supported in v1 or single-instance only? (Not in the source spec.)
- Does cancelling a `live` event have a distinct flow vs. cancelling a `published` one (refunds mid-event)?
