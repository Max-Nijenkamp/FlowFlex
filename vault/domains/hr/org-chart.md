---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.org
status: planned
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: hr.org
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Org Chart

Visual interactive org chart driven by the manager hierarchy on employee profiles. Supports tree-select for reassigning managers and department head assignment. Pure view module — owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | reads `manager_id` hierarchy + departments |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Auto-generated from `hr_employees.manager_id` hierarchy — no separate data entry
- Interactive tree: expand/collapse nodes, click to view employee profile
- Filterable by department
- Search: highlight employee in tree on search
- Download as PNG/PDF
- Manager reassignment via tree-select field (`codewithdennis/filament-select-tree`)
- Handles multi-root (companies with multiple top-level managers)

---

## Data Model

No additional tables — reads from `hr_employees` (manager_id FK) and `hr_departments`.

## DTOs

Output only: `OrgNodeData` — employee_id, full_name, job_title, department_name, photo_url, children[] (recursive). Tree built with one query + in-memory assembly (no N+1 recursion).

## Services & Actions

- `OrgChartService::tree(?string $departmentId = null): array<OrgNodeData>` — single query, cycle-safe (cycles prevented at write time by hr.profiles)
- `ReassignManagerAction::run(string $employeeId, ?string $newManagerId): void` — delegates to `EmployeeService::update` (cycle check there)

---

## Filament

**Nav group:** Employees

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `OrgChartPage` | #11 tree-view custom page | Livewire + Alpine/JS tree render in Blade; dept filter in header; PNG/PDF export *(assumed: client-side render-to-image)* |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('hr.org.view-any') && BillingService::hasModule('hr.org')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`hr.org.view` · `hr.org.reassign`

---

## Test Checklist

- [ ] Tenant isolation: tree contains only current company employees
- [ ] Module gating: page hidden when `hr.org` inactive
- [ ] Multi-root companies render all roots
- [ ] Department filter prunes tree correctly
- [ ] Reassignment updates `manager_id` and re-renders; cycle attempt rejected
- [ ] Tree built without N+1 (single query assertion)

---

## Build Manifest

```
app/Data/HR/OrgNodeData.php
app/Services/HR/OrgChartService.php
app/Actions/HR/ReassignManagerAction.php
app/Filament/HR/Pages/OrgChartPage.php
resources/views/filament/hr/pages/org-chart.blade.php
tests/Feature/HR/OrgChartTest.php
```

---

## Related

- [[domains/hr/employee-profiles]]
- [[architecture/patterns/custom-pages]]
- [[architecture/packages]] (`codewithdennis/filament-select-tree`)
