---
domain: projects
module: milestones
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Milestones — Unknowns

## Assumed Items

- Status is a plain enum, not a spatie state machine *(assumed)*.
- Single 7-day reminder window *(assumed)* — no escalating cadence.

## Open Questions

- Should `missed` milestones be reopenable if their target date is pushed?
- Multiple reminder windows (e.g. 14d/7d/1d) — configurable per company?
- Should milestone achievement fire a cross-domain event (e.g. to notify a CRM client contact)?
