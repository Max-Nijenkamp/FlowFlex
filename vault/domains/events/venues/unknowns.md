---
domain: events
module: venues
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Venues — Unknowns

## Assumed Items

- Delete is blocked while upcoming events reference the venue *(assumed)* — the exact "upcoming" cutoff (start_at > now?) is unspecified.
- Venue contact detail is stored unencrypted *(assumed)*.

## Open Questions

- Should the session room reference be a FK to `ev_venue_rooms` or a free-text string? (Cross-cuts [[../events/unknowns]].)
- Is venue cost a single figure or a per-event cost (venues used across multiple events at different rates)?
- Should venue capacity cap the event capacity automatically, or is it advisory only?
