---
domain: support
module: sla
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# SLA Management — Unknowns & Assumed Items

## Assumed Items

- **Warning at 80%** *(assumed)* — hard-coded threshold; likely should be per-policy configurable.
- **Pause windows from status timestamps** *(assumed)* — assumes the ticket audit log reliably records every `waiting_on_customer` entry/exit.

## Open Questions

- Multiple SLA breaches on reopened tickets: does reopening reset the resolution timer or continue it? Assumed continue (events already fired stay fired). Needs confirmation.
- Should `warning_sent` also notify the manager, or only the assignee? Assumed assignee + manager on breach, assignee-only on warning.

## Related

- [[./decisions]] · [[../_index|Support MOC]]
