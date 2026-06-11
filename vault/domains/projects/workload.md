---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.workload
status: planned
priority: p2
depends-on: [projects.tasks, core.billing, core.rbac]
soft-depends: [hr.profiles, projects.resources]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: projects.workload
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Workload

Team capacity view showing each member's task load per day. Identifies overloaded and underutilised team members for rebalancing. Pure view module — owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/tasks\|projects.tasks]] | assignee + estimated_hours + due_date inputs |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/hr/employee-profiles\|hr.profiles]] | capacity from HR record *(assumed: default 8h/day without it)* |
| Soft | [[domains/projects/resource-allocation\|projects.resources]] | allocation overlay |

---

## Core Features

- Workload heat-map grid: team members (rows) × days/weeks (columns)
- Cell value: estimated hours of tasks due that day per person (task hours spread evenly start→due *(assumed: due-date bucket v1, spreading later)*)
- Colour coding: green (<80%), amber (80–100%), red (>100%) of capacity *(assumed thresholds)*
- Capacity setting per user: default 8h/day; HR-driven when active
- Drag a task to reassign or reschedule directly from the workload view (via `UpdateTaskAction`)
- Filter by project, team, date range
- Overload alerts: highlight members over daily capacity
- Considers only tasks in `todo` / `in_progress` status

---

## Data Model

No additional tables. Reads `proj_tasks` + capacity (user setting / HR).

## DTOs

Output only: `WorkloadGridData` — rows[] (user, capacity_hours, cells[{date, hours, level}]).

## Services & Actions

- `WorkloadService::grid(CarbonImmutable $from, CarbonImmutable $to, WorkloadFilterData $filters): WorkloadGridData` — single aggregate query
- Mutations via `UpdateTaskAction` (reassign/reschedule)

---

## Filament

**Nav group:** Projects

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WorkloadPage` | #5-style heat-map custom page | Livewire + Alpine grid; drag-to-reassign; filters in header; polling 60s |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('projects.workload.view-any') && BillingService::hasModule('projects.workload')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`projects.workload.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Cell sums only todo/in_progress task hours on due date
- [ ] Colour level boundaries at 80%/100% of capacity
- [ ] HR capacity used when active; 8h default otherwise
- [ ] Drag reassign routes through task validation
- [ ] Single aggregate query (no N+1)

---

## Build Manifest

```
app/Data/Projects/{WorkloadGridData,WorkloadFilterData}.php
app/Services/Projects/WorkloadService.php
app/Filament/Projects/Pages/WorkloadPage.php
resources/views/filament/projects/pages/workload.blade.php
tests/Feature/Projects/WorkloadTest.php
```

---

## Related

- [[domains/projects/tasks]]
- [[domains/projects/resource-allocation]]
