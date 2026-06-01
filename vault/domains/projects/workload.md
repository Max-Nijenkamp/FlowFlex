---
type: module
domain: Projects & Work
panel: projects
module-key: projects.workload
status: planned
color: "#4ADE80"
---

# Workload

Team capacity view showing each member's task load per day. Identifies overloaded and underutilised team members for rebalancing.

## Core Features

- Workload heat-map grid: team members (rows) × days/weeks (columns)
- Cell value: sum of estimated hours for tasks due that day per person
- Colour coding: green (under capacity), amber (near capacity), red (over capacity)
- Capacity setting per user: default working hours per day (from HR if active, else default 8h)
- Drag a task to reassign or reschedule directly from the workload view
- Filter by project, team, date range
- Overload alerts: highlight members assigned more than their daily capacity
- Considers only tasks in `todo` / `in_progress` status

## Data Model

No additional tables. Reads from `proj_tasks` (assignee, estimated_hours, due_date) and user capacity setting.

## Filament

**Nav group:** Projects

- `WorkloadPage` (custom Filament page) — heat-map grid via Livewire + Alpine.js
- Date range + team filter in header

## Related

- [[domains/projects/tasks]]
- [[domains/projects/resource-allocation]]
