---
domain: projects
module: resource-allocation
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Resource Allocation — Unknowns

## Assumed Items

- Over-100% warns, not blocks *(assumed)*.
- Allocation modelled as % over a date range, not hours *(assumed)*.

## Open Questions

- Should capacity respect HR working-time/leave (integrate `hr.profiles` + `hr.leave`) for true availability?
- Cost dimension: allocation × rate → forecast project cost — in scope here or in Projects budget?
- Role-based allocation (allocate a role, later assign a person) — future?
