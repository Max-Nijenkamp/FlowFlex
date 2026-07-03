---
domain: projects
module: workload
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Workload

Team capacity view showing each member's task load per day; identifies overloaded and underutilised members for rebalancing. **Pure view module — owns no tables.**

## Module-key

`projects.workload`

**Priority:** p2  
**Panel:** projects  
**Permission prefix:** `projects.workload`  
**Tables:** *(none — reads `proj_tasks` + capacity source)*

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tasks/_module\|projects.tasks]] | assignee + estimated_minutes + due_date inputs |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../hr/employee-profiles/_module\|hr.profiles]] | capacity from HR record *(assumed: default 8h/day without it)* |
| Soft | [[../resource-allocation/_module\|projects.resources]] | allocation overlay |

## Core Features

- Heat-map grid: members (rows) × days/weeks (columns).
- Cell = estimated hours of tasks due that day per person (spread start→due *(assumed: due-date bucket v1)*).
- Colour: green (<80%), amber (80–100%), red (>100%) of capacity *(assumed thresholds)*.
- Capacity per user: default 8h/day; HR-driven when active.
- Drag a task to reassign/reschedule from the workload view (via `UpdateTaskAction`).
- Filter by project/team/date; overload alerts; considers only `todo`/`in_progress`.

## See features/

- [[features/workload-heatmap|Workload Heat-map]] — the grid, capacity colouring, drag-to-rebalance.

## Build Manifest

```
app/Data/Projects/{WorkloadGridData,WorkloadFilterData}.php
app/Services/Projects/WorkloadService.php
app/Filament/Projects/Pages/WorkloadPage.php
resources/views/filament/projects/pages/workload.blade.php
tests/Feature/Projects/WorkloadTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot render or rebalance company B's tasks/capacity on the grid.
- [ ] Module gating: artifacts hidden when `projects.workload` inactive.
- [ ] Project-membership scoping: non-member cannot open the workload grid for a project.
- [ ] Cell sums only `todo`/`in_progress` task hours on the due date.
- [ ] Colour level boundaries at 80%/100% of capacity.
- [ ] HR capacity used when active; 8h default otherwise.
- [ ] Drag reassign routes through task validation.
- [ ] Single aggregate query (no N+1).

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | `UpdateTaskAction` | projects.tasks | drag reassign/reschedule |
| Reads | capacity (hours/day) | hr.profiles | default 8h when HR inactive |
| Reads | allocation overlay | projects.resources | combined capacity |

**Data ownership:** Workload owns **no tables**. It reads `proj_tasks` + HR capacity and mutates tasks only via `UpdateTaskAction` — never a direct write into `proj_*` or `hr_*` ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../tasks/_module|Tasks]] · [[../resource-allocation/_module|Resource Allocation]]
- [[../../../glossary]]
