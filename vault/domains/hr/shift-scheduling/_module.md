---
domain: hr
module: shift-scheduling
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Shift Scheduling

Shift creation, team schedule publishing, swap requests, and coverage gap detection. For companies with rotating shifts or hourly workers.

> **Rebuild blueprint.** HR domain code was stripped under [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing below is built, shipped, or tested yet — this spec is the intended rebuild target. `build-status: planned`.

---

## Module-key

`hr.shifts`

**Priority:** v1  
**Panel:** hr  
**Permission prefix:** `hr.shifts`  
**Tables:** `hr_shifts`, `hr_shift_swap_requests`

Nav group: Leave. Consumes `LeaveRequestApproved` (from hr.leave); fires none. Encrypted fields: none.

---

## Core Features

- Managers create shifts (start/end time, role, optional employee assignment) and lay them out on a weekly per-team calendar.
- Schedules move draft → published; on publish, assigned employees are to be notified.
- Employees can request to swap a shift with a colleague; the recipient accepts and a manager approves, which reassigns the shifts.
- Unassigned shifts surface as coverage gaps so managers can fill them.
- Copying the previous week's schedule is intended to cut weekly setup effort.
- Approved leave should make an employee unassignable for the leave range, and an incoming `LeaveRequestApproved` should unassign them from existing shifts in range.

See [[features/shift-calendar]], [[features/shift-assignment]], [[features/swap-requests]], [[features/leave-conflict-blocking]], [[features/coverage-gaps]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module]] (`hr.profiles`) | shifts assigned to employees |
| Hard | `core.billing` + `core.rbac` + `core.notifications` | module gating, permissions, publish notifications |
| Soft | [[../leave-management/_module]] (`hr.leave`) | consumes `LeaveRequestApproved` to block scheduling over leave |
| Soft | [[../time-attendance/_module]] (`hr.time`) | planned-vs-actual comparison |

---

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Consumes | `LeaveRequestApproved` | [[../leave-management/_module\|hr.leave]] | `BlockShiftsOnLeaveListener` unassigns shifts overlapping approved leave, flags coverage gaps, notifies manager |
| Fires | none | — | — |

**Data ownership:** owns `hr_shifts`, `hr_shift_swap_requests`. Reads `hr_employees` (via EmployeeService) and approved leave (via LeaveService / event payload); never writes another domain's tables — [[../../../security/data-ownership]].

## Notes in this folder

- [[architecture]] — services/actions, calendar page, swap-request flow + state diagram
- [[data-model]] — `hr_shifts`, `hr_shift_swap_requests` + ERD
- [[api]] — DTOs, service methods, consumed events
- [[security]] — permissions, authz, tenancy
- [[unknowns]] — assumptions and open questions

### Feature slices

- [[features/shift-calendar]]
- [[features/shift-assignment]]
- [[features/swap-requests]]
- [[features/leave-conflict-blocking]]
- [[features/coverage-gaps]]

---

## Build Manifest

```
database/migrations/xxxx_create_hr_shifts_table.php
database/migrations/xxxx_create_hr_shift_swap_requests_table.php
app/Models/HR/{Shift,ShiftSwapRequest}.php
app/Data/HR/{CreateShiftData,RequestSwapData,ShiftData}.php
app/Contracts/HR/ShiftServiceInterface.php
app/Services/HR/ShiftService.php
app/Exceptions/HR/{ShiftConflictException,EmployeeOnLeaveException}.php
app/Listeners/HR/BlockShiftsOnLeaveListener.php
app/Filament/HR/Pages/ShiftSchedulePage.php
app/Filament/HR/Resources/ShiftSwapRequestResource.php
database/factories/HR/{ShiftFactory,ShiftSwapRequestFactory}.php
tests/Feature/HR/{ShiftSchedulingTest,ShiftSwapTest,LeaveBlockingTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot see, assign, or publish company B shifts or swap requests
- [ ] Module gating: artifacts hidden when `hr.shifts` inactive
- [ ] Overlapping-shift assignment rejected (`ShiftConflictException`); assigning an employee on approved leave rejected (`EmployeeOnLeaveException`)
- [ ] `publishWeek` flips that week's drafts → published and notifies assigned employees (`panel-action` limiter)
- [ ] `copyWeek` copies shifts as drafts and skips employees on leave
- [ ] Swap approval reassigns both shifts atomically under `lockForUpdate`
- [ ] `LeaveRequestApproved` unassigns overlapping shifts and flags coverage gaps (listener under `WithCompanyContext`)
- [ ] CRUD stale-write raises `StaleRecordException`

---

## Related

- [[../leave-management/_module]] — event source (`LeaveRequestApproved`)
- [[../time-attendance/_module]] — soft-dep (planned-vs-actual)
- [[../employee-profiles/_module]] — assignment target
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
