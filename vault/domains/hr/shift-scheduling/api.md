---
domain: hr
module: shift-scheduling
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Shift Scheduling — API (DTOs, Services, Events)

Intended contracts (not yet built). See [[_module]].

## DTOs (spatie/laravel-data)

### CreateShiftData
- `date` (required)
- `start_time` / `end_time` (required, valid span)
- `role` (required)
- `employee_id` (nullable; if set: no overlap with employee's other shifts, no approved leave on date)

### RequestSwapData
- `shift_id` (own shift)
- `recipient_id` (different employee, no conflict on that date)

### ShiftData
- output DTO returned from service methods

**Validation messages:** "This employee is on approved leave that day." · "This employee already has a shift overlapping this time."

## Services

`ShiftServiceInterface` → `ShiftService` (methods listed in [[architecture]]).

Exceptions: `ShiftConflictException`, `EmployeeOnLeaveException`.

## Events

**Fires:** none.

**Consumes:** `LeaveRequestApproved` (from [[../leave-management/_module]]).

Listener `BlockShiftsOnLeaveListener` — queued (`ShouldQueue`), `WithCompanyContext`. On approved leave it should:
1. unassign the employee from published/draft shifts overlapping the leave range,
2. flag the newly-vacated shifts as coverage gaps,
3. notify the manager.

Payload must match the contract in [[../../../architecture/event-bus]] (carries `company_id` as a scalar). Notifications route via `core.notifications` — see [[../../../infrastructure/mail]].

## Related

- [[architecture]] · [[data-model]] · [[security]]
- [[features/leave-conflict-blocking]]
- [[../../../architecture/event-bus]]
