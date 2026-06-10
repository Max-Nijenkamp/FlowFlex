---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.gantt
status: planned
priority: p2
depends-on: [projects.tasks, projects.milestones, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: projects.gantt
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Gantt Chart

Timeline view of tasks and milestones with dependency arrows. Shows project schedule at a glance and highlights critical path. Pure view module — owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/tasks\|projects.tasks]] + [[domains/projects/milestones\|projects.milestones]] | bars + markers + dependency arrows |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Horizontal Gantt timeline: tasks as bars on a date axis (bar = start *(assumed: created/start = due − estimated days)* → due date)
- Milestone markers on the timeline
- Dependency arrows between tasks (blocks/blocked-by)
- Zoom levels: day / week / month view
- Drag bar to reschedule task (updates due date via `UpdateTaskAction`)
- Drag bar edge to resize duration (updates estimated hours)
- Highlight critical path (longest chain of dependencies by duration)
- Colour-coded bars by assignee or priority
- Today marker line
- Export as PNG/PDF (client-side render *(assumed)*)

---

## Data Model

No additional tables. Reads from `proj_tasks`, `proj_task_dependencies`, `proj_milestones`.

## DTOs

Output only: `GanttData` — tasks[] (id, title, start, end, progress, dependencies[]), milestones[] (id, title, date), critical_path_ids[].

## Services & Actions

- `GanttService::data(string $projectId): GanttData` — single query set; critical path via longest-path walk over dependency DAG (cycle-free guaranteed by tasks module)
- Mutations delegate to `UpdateTaskAction` (due date / estimate)

---

## Filament

**Nav group:** Projects

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `GanttChartPage` | #5 Gantt custom page | `frappe-gantt` (or similar) in Blade partial via Alpine bridge; project selector + zoom in header; polling 60s |

JS lib bundled in panel theme (vite) — no CDN *(assumed)*.

---

## Permissions

`projects.gantt.view` (+ task permissions for drag mutations).

---

## Test Checklist

- [ ] Tenant isolation + module gating + membership scoping
- [ ] GanttData includes bars, markers, dependency edges (fixture project)
- [ ] Critical path correct on branched DAG fixture
- [ ] Drag reschedule routes through UpdateTaskAction validation
- [ ] No N+1 building the dataset

---

## Build Manifest

```
app/Data/Projects/GanttData.php
app/Services/Projects/GanttService.php
app/Filament/Projects/Pages/GanttChartPage.php
resources/views/filament/projects/pages/gantt-chart.blade.php
resources/js/gantt.js (lib bridge, bundled via vite theme)
tests/Feature/Projects/GanttTest.php
```

---

## Related

- [[domains/projects/tasks]]
- [[domains/projects/milestones]]
- [[architecture/patterns/custom-pages]]
- [[architecture/ui-strategy]] — row #5
