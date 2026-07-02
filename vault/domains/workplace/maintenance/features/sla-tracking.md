---
domain: workplace
module: maintenance
feature: sla-tracking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# SLA Tracking

Flag requests that miss their priority-based resolution target.

## Behaviour

- Each priority (urgent/high/normal/low) has a resolution-target in days *(assumed — exact targets undocumented)*.
- A request past its target without resolution is flagged **overdue**.
- The "overdue" queue tab surfaces breached requests.

## UI

- **Kind**: simple-resource (overdue tab + column on the request resource)
- **Page**: "Overdue" tab + SLA/overdue column on `MaintenanceRequestResource`.
- **Layout**: overdue badge (red) on breached rows; sort by age; filter by priority.
- **Key interactions**: switch to "Overdue" tab → breached requests only; overdue chip on each row.
- **States**: empty (nothing overdue → "all on track") · loading (skeleton) · error (toast) · selected (row → detail).
- **Gating**: `workplace.maintenance.view-any`.

## Data

- Owns / writes: nothing (computed over `wp_maintenance_requests`).
- Reads: `wp_maintenance_requests` (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

> [!warning] UNVERIFIED
> Exact SLA target days per priority are undocumented *(assumed)* — see [[../unknowns]].

## Relations

- Consumes: nothing.
- Feeds: SLA-breach metrics read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: none.

## Related

- [[../_module|Facility Maintenance]] · [[assignment-workflow]] · [[../unknowns]]
