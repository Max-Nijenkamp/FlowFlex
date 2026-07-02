---
domain: projects
module: gantt
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Gantt Chart

Timeline view of tasks and milestones with dependency arrows; shows the project schedule at a glance and highlights the critical path. **Pure view module — owns no tables.**

## Module-key

| Field | Value |
|---|---|
| key | `projects.gantt` |
| priority | p2 |
| panel | projects |
| permission-prefix | `projects.gantt` |
| tables | *(none — reads `proj_tasks`, `proj_task_dependencies`, `proj_milestones`)* |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tasks/_module\|projects.tasks]] + [[../milestones/_module\|projects.milestones]] | bars + markers + dependency arrows |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |

## Core Features

- Horizontal timeline: tasks as bars on a date axis (bar = start *(assumed: due − estimated days)* → due).
- Milestone markers; dependency arrows (blocks/blocked-by).
- Zoom: day / week / month.
- Drag bar to reschedule (updates due via `UpdateTaskAction`); drag edge to resize (updates estimate).
- Highlight critical path (longest dependency chain by duration).
- Colour by assignee/priority; today marker; export PNG/PDF (client-side *(assumed)*).

## See features/

- [[features/timeline-view|Timeline & Critical Path]] — the Gantt render, dependency arrows, drag-reschedule, critical path.

## Build Manifest

```
app/Data/Projects/GanttData.php
app/Services/Projects/GanttService.php
app/Filament/Projects/Pages/GanttChartPage.php
resources/views/filament/projects/pages/gantt-chart.blade.php
resources/js/gantt.js (frappe-gantt bridge, bundled via vite theme — no CDN *(assumed)*)
tests/Feature/Projects/GanttTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating + membership scoping.
- [ ] `GanttData` includes bars, markers, dependency edges (fixture project).
- [ ] Critical path correct on a branched DAG fixture.
- [ ] Drag reschedule routes through `UpdateTaskAction` validation.
- [ ] No N+1 building the dataset.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | `UpdateTaskAction` | projects.tasks | drag-reschedule / resize |
| Reads | milestone markers | projects.milestones | timeline markers |

**Data ownership:** Gantt owns **no tables**. It reads `proj_tasks`, `proj_task_dependencies`, `proj_milestones` and mutates tasks only via `UpdateTaskAction` — never a direct write ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../tasks/_module|Tasks]] · [[../milestones/_module|Milestones]] · [[../../../architecture/ui-strategy]]
- [[../../../glossary]]
