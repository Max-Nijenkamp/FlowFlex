---
domain: hr
module: shift-scheduling
feature: shift-assignment
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Shift Assignment

## Purpose

Create a shift and optionally assign it to an employee, enforcing conflict rules.

## Intended Behavior

- `createShift(CreateShiftData)` sets date, start/end time, role, and optional `employee_id`.
- If assigned: reject when the employee has an overlapping shift (`ShiftConflictException`) or approved leave on that date (`EmployeeOnLeaveException`).
- Leaving `employee_id` null creates an unassigned shift (a coverage gap).

## Tables / Permissions / Events

- Tables: `hr_shifts`
- Permissions: `hr.shifts.create`, `hr.shifts.update`
- Events: none fired

## UI

- **Kind**: custom-page (drag-assign on the shift calendar) *(a simple `ShiftResource` create/edit form is the fallback shape)*
- **Page**: "Shift Schedule" (`/hr/shift-schedule`) — assignment happens inline on the calendar page
- **Layout**: create-shift form/modal (date, start/end time, role, optional employee) and drag-to-assign on the roster grid; leaving employee empty creates an unassigned shift (coverage gap).
- **Key interactions**: `createShift` with optional `employee_id`; drag a shift onto an employee row to assign; reassign/clear assignment.
- **States**: empty (unassigned shift renders as a highlighted gap) · loading (grid skeleton) · error (toast on `ShiftConflictException` / `EmployeeOnLeaveException`) · selected (shift popover showing assignee + conflict warnings).
- **Gating**: create requires `hr.shifts.create`; update/assign requires `hr.shifts.update`.

## Data

- Owns / writes: `hr_shifts`
- Reads: reads `hr_employees` via EmployeeService (assignee, existing shifts for overlap); reads approved leave via LeaveService (on-leave check)
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none *(reactive leave handling lives in [[leave-conflict-blocking]])*
- Feeds: none
- Shared entity: `hr_employees` (read via EmployeeService); approved leave read via LeaveService

## Related

- [[../_module]] · [[../api]] · [[features/leave-conflict-blocking]]
