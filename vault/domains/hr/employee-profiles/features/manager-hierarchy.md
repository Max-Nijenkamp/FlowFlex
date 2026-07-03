---
domain: hr
module: employee-profiles
feature: manager-hierarchy
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Manager Hierarchy

> Planned vertical slice. Back to [[../_module]].

## Purpose

Self-referential reporting structure on `hr_employees` (recursive `manager_id` FK), enabling direct-reports listing and upward manager-chain resolution for approval routing.

## Behavior

- `manager_id` self-FK; must reference an employee in the same company, not self.
- `EmployeeService::update` rejects circular chains with `ManagerCycleException`.
- `directReports(employeeId)` lists an employee's direct reports.
- `managerChain(employeeId)` returns the upward chain (used by approval routing).
- Direct reports list shown on the employee profile.
- Soft-depends on [[../../org-chart/_module|hr.org]] to render the hierarchy; without it, the hierarchy exists data-only.

## UI

- **Kind**: simple-resource
- **Page**: Employee form (`/hr/employees/{id}/edit`)
- **Layout**: `manager_id` select field on the Employment tab (searchable employee select), cycle-prevented at write time (`ManagerCycleException`).
- **Key interactions**: pick or change an employee's manager.
- **States**: empty = no manager (top-level) · loading = async select options · error = cycle rejected with a message · selected = chosen manager shown.
- **Gating**: visible with `hr.employees.view`; edit requires `hr.employees.update`.

> [!warning] UNVERIFIED
> The visual reporting tree lives in hr.org (org-chart module), not here; this feature only writes `manager_id`.

## Data

- Owns / writes: `hr_employees.manager_id`.
- Reads: `hr_employees` (self, for select options).
- Cross-domain writes: none.

## Relations

- Consumes: none.
- Feeds: none (manager_id changes read by hr.org).
- Shared entity: reads `hr_employees` self-referential.

## Test Checklist

### Unit
- [ ] Cycle detection rejects self-reference, direct cycle (A→B→A), and indirect cycle
- [ ] `manager_id` must reference an employee in the same company

### Feature (Pest)
- [ ] `EmployeeService::update` throws `ManagerCycleException` on a circular chain; record unchanged
- [ ] `directReports` / `managerChain` resolve the correct sets
- [ ] Cross-company manager assignment is rejected (tenant isolation)

### Livewire
- [ ] Setting a cycling manager surfaces the `ManagerCycleException` message inline; edit requires `hr.employees.update`

## Related

- Column: `hr_employees.manager_id` (index `(company_id, manager_id)`) — [[../data-model]]
- Service: `directReports`, `managerChain`; exception `ManagerCycleException` — [[../architecture]]
- Soft dep: [[../../org-chart/_module|hr.org]]
