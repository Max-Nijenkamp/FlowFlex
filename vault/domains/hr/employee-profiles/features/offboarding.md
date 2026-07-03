---
domain: hr
module: employee-profiles
feature: offboarding
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Offboarding

> Planned vertical slice. Back to [[../_module]].

## Purpose

Terminate employment: capture termination date, reason, and downstream signals (exit survey link, equipment checklist), transitioning the employee to `terminated`.

## Behavior

- `EmployeeService::offboard(OffboardEmployeeData)` transitions state to `terminated` and fires `EmployeeOffboarded`.
- Requires `termination_date` (on/after `hire_date`) and `termination_reason` (max:1000).
- Surfaced as `OffboardAction` (modal) on the employee view page.
- Gated by `hr.employees.offboard`.
- `EmployeeOffboarded` consumers per event bus: final pay + access revocation; IT provisioning (P3).

## UI

- **Kind**: simple-resource (a modal Offboard action on the Employee view page; offboarding here is a termination form modal, not a standalone wizard — the richer checklist lives in a future offboarding flow).
- **Page**: Offboard action on the Employee view (`/hr/employees/{id}`)
- **Layout**: modal form (termination_date, termination_reason, final-day options) that transitions the state to `terminated`.
- **Key interactions**: submit the termination form to offboard the employee.
- **States**: empty = n/a · loading = submit spinner · error = missing reason/date rejected · selected = confirmation.
- **Gating**: visible with `hr.employees.view`; offboard requires `hr.employees.update` *(assumed — could be a dedicated `hr.employees.offboard` permission)*.

## Data

- Owns / writes: `hr_employees` (status / termination fields).
- Reads: none.
- Cross-domain writes: via events only (`EmployeeOffboarded`) — never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: `EmployeeOffboarded` → consumed by IT deprovisioning *(P3 soft)*, Payroll final pay, hr.self-service access revoke.
- Shared entity: none.

> [!warning] UNVERIFIED
> IT deprovisioning as an `EmployeeOffboarded` consumer is a P3 soft integration and not confirmed by any built spec.

## Test Checklist

### Unit
- [ ] `OffboardEmployeeData` requires `termination_date` (on/after `hire_date`) and `termination_reason` (max:1000)

### Feature (Pest)
- [ ] `offboard` transitions to `terminated` and fires `EmployeeOffboarded` with the contract payload
- [ ] Missing/invalid termination date or reason is rejected; state unchanged
- [ ] The transition takes a pessimistic lock (`DB::transaction` + `lockForUpdate`) — concurrent double-offboard is serialized ([[../architecture]])

### Livewire
- [ ] `OffboardAction` modal validates required fields; action gated on `hr.employees.offboard`

## Related

- Columns: `hr_employees.termination_date`, `termination_reason`, `status` — [[../data-model]]
- DTO: `OffboardEmployeeData` — [[../api]]
- Event: `EmployeeOffboarded` — [[../api]] · [[../../../../architecture/event-bus]]
- Permission: `hr.employees.offboard` — [[../security]]
