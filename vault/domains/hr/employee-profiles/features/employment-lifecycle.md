---
domain: hr
module: employee-profiles
feature: employment-lifecycle
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Employment Lifecycle (State Machine)

> Planned vertical slice. Back to [[../_module]].

## Purpose

Manage employment status over time via a `spatie/laravel-model-states` machine on `hr_employees.status`.

## Behavior

- States: `active` (initial, on hire) → `on_leave` | `suspended` | `terminated` (terminal).
- `active ↔ on_leave` / `active ↔ suspended` via `hr.employees.update`.
- `on_leave → active` and `suspended → active` via `hr.employees.update`.
- Any non-terminal state → `terminated` via `hr.employees.offboard` (see [[offboarding]]).
- Suspension disables portal login *(assumed)*. `on_leave` may later auto-trigger from approved long leave *(assumed: manual v1)*.
- All transitions audited (activitylog).

Full transition table + Mermaid diagram in [[../architecture]].

## UI

- **Kind**: simple-resource
- **Page**: "Employee Record" (`/hr/employees/{id}`)
- **Layout**: status badge on the view-page header; modal actions (Put On Leave, Suspend, Reactivate, Terminate) driven by the `active → on_leave | suspended | terminated` state machine.
- **Key interactions**: trigger a legal transition via a modal action.
- **States**: empty = n/a (always has a status, default active) · loading = action modal spinner · error = illegal transition rejected with a message · selected = current state highlighted, only legal transitions shown.
- **Gating**: visible with `hr.employees.view`; transitions require `hr.employees.update`.

## Data

- Owns / writes: `hr_employees` (status column + termination_date / termination_reason).
- Reads: none.
- Cross-domain writes: via events only (`EmployeeOffboarded`) — never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: `EmployeeOffboarded` → consumed by IT deprovisioning *(P3 soft)*, Payroll (final pay), hr.self-service (access revoke).
- Shared entity: none.

> [!warning] UNVERIFIED
> IT deprovisioning as an `EmployeeOffboarded` consumer is a P3 soft integration and not confirmed by any built spec.

## Related

- Column: `hr_employees.status` — [[../data-model]]
- States: `app/States/HR/Employee/{EmployeeState,Active,OnLeave,Suspended,Terminated}.php`
- Permissions: `hr.employees.update`, `hr.employees.offboard` — [[../security]]
- [[../../../../architecture/patterns/states]]
