---
domain: workplace
module: maintenance
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Facility Maintenance — Unknowns

## Assumed Items

- Auto-close of `resolved` requests after 7 days *(assumed)*.
- SLA = resolution-target days per priority, with an overdue flag *(assumed)* — exact targets undocumented.
- Contractor is a free-text string, not a managed vendor *(assumed)*.
- No cross-domain event fired *(assumed)*.

## Open Questions

- Should resolution fire a `MaintenanceResolved` event (finance contractor cost, asset service history)?
- What are the SLA target days per priority (urgent/high/normal/low)?
- Should external contractors link to a procurement/vendor record when that domain ships?
- Are cost/labour fields tracked per request (for facility budgeting)?
- Should reporters be able to rate/confirm resolution quality?
- Does "safety" category trigger any escalation / incident linkage?
