---
domain: hr
module: org-chart
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Org Chart

Visual interactive org chart driven by the manager hierarchy on employee profiles. Intended to support tree-select for reassigning managers and department head assignment. Pure view module — owns no tables; it reads data owned by [[../employee-profiles/_module|hr.profiles]].

> Rebuild blueprint. All HR code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing here is built, shipped, or tested — this spec is the intended target.

- **module-key:** `hr.org`
- **panel:** hr · **nav group:** Employees
- **priority:** v1
- **pattern:** [[../../../architecture/patterns/custom-pages|custom-pages]]

---

## Intended Behavior

- Auto-generated from `hr_employees.manager_id` hierarchy — no separate data entry.
- Interactive tree: expand/collapse nodes, click to view employee profile.
- Filterable by department.
- Search: highlight employee in tree on search.
- Download as PNG/PDF.
- Manager reassignment via tree-select field (`codewithdennis/filament-select-tree`).
- Handles multi-root (companies with multiple top-level managers).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module|hr.profiles]] | reads `manager_id` hierarchy + departments |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |

Data source: [[../employee-profiles/_module|hr.profiles]] owns `hr_employees` and `hr_departments`.

---

## Cross-Domain Edges

| Direction | Event | Counterpart |
|---|---|---|
| Consumes | `EmployeeHired` / `EmployeeOffboarded` *(assumed — may re-query live)* | hr.profiles |
| Fires | — | none |
| Writes-via-owner | `hr_employees.manager_id` through hr.profiles service | hr.profiles |

Owns **no tables** — pure view module. Reads `hr_employees` + `hr_departments` (owned by hr.profiles) via its read API; any `manager_id` reassignment goes through hr.profiles' owning service, never a direct cross-domain write ([[../../../security/data-ownership]]).

---

## Notes in this folder

- [[architecture]] — services, actions, custom Filament page approach, tree-build flow
- [[data-model]] — read relationships (no owned tables)
- [[api]] — `OrgNodeData` DTO + `OrgChartService` / `ReassignManagerAction`
- [[security]] — permissions, authz, tenancy
- [[unknowns]] — assumptions + unverified items

### Features
- [[features/org-tree-visualization]]
- [[features/department-filter]]
- [[features/manager-reassignment]]
- [[features/export]]

---

## Test Checklist (intended)

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

- [[../employee-profiles/_module]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../glossary]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
