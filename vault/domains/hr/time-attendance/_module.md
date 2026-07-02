---
domain: hr
module: time-attendance
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Time & Attendance

Clock-in/out, timesheet management, overtime tracking, and approval workflow. Intended to integrate with Payroll for hourly employees. This is a rebuild blueprint — HR domain code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing below is built, shipped, or tested yet.

- Module key: `hr.time` · Panel: `hr` · Priority: v1
- Permission prefix: `hr.time` · Encrypted fields: none

## Intended Behavior

- Clock-in/clock-out: manual entry or timer-based (in self-service portal).
- Weekly timesheet: employee fills in hours per day per project/task.
- Overtime detection when hours exceed the standard workday.
- Manager approval of timesheets before payroll run.
- Break time tracking; timesheet export for payroll integration.
- Approved timesheet is intended to fire `TimesheetApproved` → hourly payroll calculation.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | entries belong to employees |
| Hard | core.billing + core.rbac | gating + permissions |
| Soft | [[../payroll/_module\|hr.payroll]] | consumes `TimesheetApproved` for hourly pay; without it the event fires unconsumed |
| Soft | [[../shift-scheduling/_module\|hr.shifts]] | planned-vs-actual comparison *(assumed: display only)* |

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Fires | `TimesheetApproved` | [[../payroll/_module\|hr.payroll]] | hourly pay calculation from approved minutes |
| Consumes | none | — | — |

**Data ownership:** owns `hr_time_entries`, `hr_timesheets`. Reads `hr_employees` (via EmployeeService) and the standard-workday company setting; never writes another domain's tables — [[../../../security/data-ownership]].

## Notes in This Folder

- [[architecture]] — services/actions + timesheet state machine
- [[data-model]] — `hr_time_entries`, `hr_timesheets` + ERD
- [[api]] — DTOs, services, `TimesheetApproved` event
- [[security]] — permissions, authz, tenancy
- [[unknowns]] — open questions + assumptions

## Features

- [[features/time-entries]] — clock-in/out + manual logging
- [[features/timesheet-approval-workflow]] — submit / approve / reject
- [[features/overtime-detection]] — overtime flagging

## Filament

**Nav group:** Leave

| Artifact | Kind ([[../../../architecture/patterns/states|ui-strategy]] row) | Notes |
|---|---|---|
| `TimesheetResource` | #1 CRUD resource | pending-approval tab; approve/reject actions |
| `TimeEntryResource` | #1 CRUD resource | entries list, filters by employee/date |
| Clock widget (self-service dashboard) | #6 widget | clock in/out button + running timer |

## Build Manifest

```
database/migrations/xxxx_create_hr_time_entries_table.php
database/migrations/xxxx_create_hr_timesheets_table.php
app/Models/HR/{TimeEntry,Timesheet}.php
app/States/HR/Timesheet/{TimesheetState,Draft,Submitted,Approved,Rejected}.php
app/Data/HR/{LogTimeEntryData,SubmitTimesheetData,TimesheetData,TimeEntryData}.php
app/Contracts/HR/TimeServiceInterface.php
app/Services/HR/TimeService.php
app/Exceptions/HR/AlreadyClockedInException.php
app/Events/HR/TimesheetApproved.php
app/Filament/HR/Resources/{TimesheetResource,TimeEntryResource}.php
app/Filament/HR/Widgets/ClockWidget.php
database/factories/HR/{TimeEntryFactory,TimesheetFactory}.php
tests/Feature/HR/{TimeTrackingTest,TimesheetApprovalTest}.php
```

## Related

- [[../payroll/_module]] (consumer)
- [[../shift-scheduling/_module]] (soft-dep)
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
