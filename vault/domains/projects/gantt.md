---
type: module
domain: Projects & Work
panel: projects
module-key: projects.gantt
status: planned
color: "#4ADE80"
---

# Gantt Chart

Timeline view of tasks and milestones with dependency arrows. Shows project schedule at a glance and highlights critical path.

## Core Features

- Horizontal Gantt timeline: tasks as bars on a date axis
- Milestone markers on the timeline
- Dependency arrows between tasks (blocks/blocked-by)
- Zoom levels: day / week / month view
- Drag bar to reschedule task (updates due date)
- Drag bar edge to resize duration (updates estimated hours)
- Highlight critical path (longest chain of dependencies)
- Colour-coded bars by assignee or priority
- Today marker line
- Export as PNG or PDF

## Data Model

No additional tables. Reads from `proj_tasks`, `proj_task_dependencies`, `proj_milestones`.

## Filament

**Nav group:** Projects

- `GanttChartPage` (custom Filament page) — Vue component embedded via Livewire/Alpine.js bridge
- Uses `frappe-gantt` or a similar open-source Gantt library rendered in a Blade partial
- Project selector + zoom control in page header

## Related

- [[domains/projects/tasks]]
- [[domains/projects/milestones]]
- [[architecture/patterns/custom-pages]]
