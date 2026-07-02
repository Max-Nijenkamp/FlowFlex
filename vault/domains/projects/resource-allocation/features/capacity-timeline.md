---
domain: projects
module: resource-allocation
feature: capacity-timeline
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Capacity Timeline

A Gantt-style view of who is on which project when, with free-capacity forecast and plan-vs-actual.

## Behaviour

- Timeline: users (rows) × time (columns) with allocation bars per project.
- Free-capacity forecast: per-user available % over a date range (`availableCapacity`).
- Plan vs actual: allocated % vs logged time when `projects.time` is active.

## UI

- **Kind**: custom-page (timeline).
- **Page**: `AllocationTimelinePage` at `/app/projects/resources/timeline` (nav group Settings).
- **Layout**: left user list + right timeline canvas; allocation bars coloured per project; over-allocated cells flagged red; date-range + team filters in header.
- **Key interactions**: hover bar → allocation detail; date-range scrub; toggle plan-vs-actual overlay.
- **States**: empty (no allocations in range) · loading (skeleton rows) · error (toast) · selected (bar highlighted) · warning (over-allocated red cells).
- **Gating**: `projects.resources.view-any`.

## Data

- Owns / writes: nothing (read-only over own allocations).
- Reads: `proj_resource_allocations` (own) + time entries (actual) via projects.time.
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: overlay data to projects.workload (read).
- Shared entity: `proj_time_entries` (projects.time), `users`.

## Unknowns

- Cost overlay (allocation × rate); role-based allocation — see [[../unknowns]].

## Related

- [[../_module|Resource Allocation]] · [[allocation-record|Allocation Record]] · [[../../time-tracking/_module|Time Tracking]]
