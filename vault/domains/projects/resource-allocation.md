---
type: module
domain: Projects & Work
panel: projects
module-key: projects.resources
status: planned
color: "#4ADE80"
---

# Resource Allocation

Allocate team members to projects by percentage of their time. Plan capacity across multiple concurrent projects.

## Core Features

- Allocation record: user, project, percentage of time, start date, end date
- Per-user total allocation: sum across projects (warn if >100%)
- Allocation timeline: Gantt-style view of who is on which project when
- Capacity planning: forecast available capacity for new projects
- Allocation vs actual: compare planned % against logged time (from Time Tracking)
- Filter by team, project, date range
- Conflict detection: flag over-allocated members (>100% across projects)

## Data Model

| Table | Key Columns |
|---|---|
| `proj_resource_allocations` | company_id, user_id, project_id, allocation_percent, start_date, end_date |

## Filament

**Nav group:** Settings

- `ResourceAllocationResource` — list, create, edit allocations
- `AllocationTimelinePage` (custom page) — timeline grid: users × time with allocation bars

## Related

- [[domains/projects/workload]]
- [[domains/projects/time-tracking]]
- [[domains/projects/projects]]
