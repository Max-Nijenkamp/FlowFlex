---
domain: hr
module: shift-scheduling
feature: leave-conflict-blocking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Leave Conflict Blocking

## Purpose

Keep employees off shifts that overlap approved leave, both at assignment time and reactively when leave is approved.

## Intended Behavior

- Assignment-time: `createShift` rejects assigning an employee with approved leave on the date (`EmployeeOnLeaveException`).
- Copy-week: `copyWeek` skips employees on leave in the target week.
- Reactive: consuming `LeaveRequestApproved`, `BlockShiftsOnLeaveListener` (queued, `WithCompanyContext`) unassigns the employee from draft/published shifts in the leave range, flags the vacated shifts as coverage gaps, and notifies the manager.

## Tables / Permissions / Events

- Tables: `hr_shifts`
- Permissions: covered by `hr.shifts.update` (system-driven unassignment)
- Events: **consumes** `LeaveRequestApproved` (from [[../../leave-management/_module]])

## UI

- **Kind**: background (validation + queued listener on leave events — no dedicated screen)
- **Page**: none (effects surface on the shift calendar as new coverage gaps + manager notification)
- **Layout**: no own screen; assignment-time blocks raise `EmployeeOnLeaveException` (toast on the assign flow), and reactive unassignment appears as highlighted gaps on `ShiftSchedulePage`.
- **Key interactions**: none direct — `createShift`/`copyWeek` enforce the block synchronously; `BlockShiftsOnLeaveListener` runs on `LeaveRequestApproved`.
- **States**: n/a (no interactive UI) — outcomes visible via [[coverage-gaps]] and manager notification.
- **Gating**: system-driven unassignment covered by `hr.shifts.update` (no user-facing permission of its own).

## Data

- Owns / writes: `hr_shifts` (unassigns `employee_id`, flags vacated shifts as gaps — this module's own table)
- Reads: reads approved leave range via LeaveService / event payload; reads `hr_employees` via EmployeeService
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: `LeaveRequestApproved` from `hr.leave` → `BlockShiftsOnLeaveListener` (queued, `WithCompanyContext`) unassigns the employee from draft/published shifts in range, flags gaps, notifies the manager
- Feeds: none (fires no events)
- Shared entity: `hr_employees` (read via EmployeeService); approved leave read via LeaveService

## Related

- [[../_module]] · [[../api]] · [[../../../../architecture/event-bus]]
