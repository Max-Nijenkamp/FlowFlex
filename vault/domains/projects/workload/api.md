---
domain: projects
module: workload
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Workload — API / DTOs

## Input

### WorkloadFilterData
`project_id?`, `team?`, `from`, `to`. Reassign/reschedule reuse **projects.tasks** `UpdateTaskAction`.

## Output

### WorkloadGridData
`rows[]` (user, capacity_hours, `cells[]` {date, hours, level}). Single aggregate query.

## Public / Portal Endpoints

None.
