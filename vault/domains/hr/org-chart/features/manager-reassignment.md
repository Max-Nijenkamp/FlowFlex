---
domain: hr
module: org-chart
feature: manager-reassignment
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Manager Reassignment

## Purpose

Reassign an employee's manager directly from the org chart.

## Intended Behavior

- Tree-select field (`codewithdennis/filament-select-tree`) for picking the new manager.
- `ReassignManagerAction::run($employeeId, $newManagerId)` delegates to `EmployeeService::update`.
- Cycle attempts are rejected (cycle check lives in hr.profiles' `EmployeeService::update`).
- On success, `manager_id` updates and the tree re-renders.

## UI

- **Kind**: custom-page (modal action / tree-select interaction on `OrgChartPage`)
- **Page**: reassign action on the Org Chart page (`/hr/org-chart`)
- **Layout**: A tree-select field (`codewithdennis/filament-select-tree`) in a modal to pick a new manager for the selected node; on save the hierarchy re-renders.
- **Key interactions**: Open the reassign action on a node, pick a new manager, save.
- **States**: empty = n/a; loading = save spinner; error = manager-cycle rejected (`ManagerCycleException`) with an inline message; selected = new manager chosen, tree updates.
- **Gating**: visible with `hr.org.view`; reassignment requires `hr.org.reassign` *(assumed — could reuse `hr.employees.update`)*.

## Data

- Owns / writes: none (view-only module)
- Reads: `hr_employees` (self-referential `manager_id` for hierarchy) + `hr_departments`, both owned by [[../../employee-profiles/_module|hr.profiles]], read via `EmployeeService` / `OrgChartService`.
- Writes `hr_employees.manager_id` only via hr.profiles' `EmployeeService` / `ReassignManagerAction` (owning-service rule), never a direct cross-domain write. Whether the action lives in hr.org or delegates to hr.profiles is *(assumed)*.
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

> [!warning] UNVERIFIED
> Which module hosts the write is unconfirmed. `ReassignManagerAction` may live in hr.org and delegate to hr.profiles' `EmployeeService::update`, or the action may itself live in hr.profiles. Either way the actual `manager_id` mutation must go through hr.profiles' owning service — no direct write into `hr_employees` from hr.org. *(assumed)*

## Relations

- Consumes: none
- Feeds: triggers a `manager_id` update in `hr.profiles` via its service; may indirectly cause hr.profiles to fire a hierarchy-changed effect.
  > [!warning] UNVERIFIED
  > No confirmed hierarchy-changed event exists on hr.profiles. *(assumed)*
- Shared entity: reads / writes-via-owner `hr_employees` (owned by hr.profiles).

## Test Checklist

### Unit
- [ ] A reassignment producing a cycle is rejected (`ManagerCycleException`, check delegated to `EmployeeService::update`)

### Feature (Pest)
- [ ] `ReassignManagerAction` updates `hr_employees.manager_id` via hr.profiles' owning service; the tree reflects the new parent
- [ ] The `manager_id` write goes only through `EmployeeService` — never a direct cross-domain write from hr.org
- [ ] Reassigning to a manager in another company is impossible (tenant isolation)

### Livewire
- [ ] Reassign action gated on `hr.org.reassign`; the tree-select modal surfaces the `ManagerCycleException` message inline on a cycle

## Related

- Tables (read/write via hr.profiles): `hr_employees.manager_id`
- Permissions: `hr.org.reassign`
- [[../_module]]
