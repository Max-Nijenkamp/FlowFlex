---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.time
status: planned
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac]
soft-depends: [hr.payroll, hr.shifts]
fires-events: [TimesheetApproved]
consumes-events: []
patterns: [states, events]
tables: [hr_time_entries, hr_timesheets]
permission-prefix: hr.time
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Time & Attendance

Clock-in/out, timesheet management, overtime tracking, and approval workflow. Integrates with Payroll for hourly employees.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | entries belong to employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/hr/payroll\|hr.payroll]] | consumes `TimesheetApproved` for hourly pay; without it event fires unconsumed |
| Soft | [[domains/hr/shift-scheduling\|hr.shifts]] | planned-vs-actual comparison *(assumed: display only)* |

---

## Core Features

- Clock-in/clock-out: manual entry or timer-based (in self-service portal)
- Timesheet: weekly view — employee fills in hours per day per project/task
- Overtime detection: hours > standard workday (from company settings *(assumed: 8h default)*) trigger overtime flag
- Manager approval of timesheets before payroll run
- Break time tracking
- Timesheet export for payroll integration
- Approved timesheet fires `TimesheetApproved` → hourly payroll calculation

---

## Data Model

### hr_time_entries

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed), employee_id FK | ulid | | |
| date | date | not null | |
| clock_in / clock_out | time | clock_out after clock_in | nullable while running |
| break_minutes | int | default 0 | |
| total_minutes | int | computed on close | minutes, not decimal hours |
| is_overtime | boolean | default false | |
| notes | text | nullable | |
| timesheet_id | ulid | nullable FK | linked on submission |

**Indexes:** `(company_id, employee_id, date)` unique *(assumed: one entry per day v1; multiple via separate rows if needed → relax later)*

### hr_timesheets

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), employee_id FK | ulid | unique `(company_id, employee_id, week_start)` |
| week_start | date | Monday per company week-start setting |
| total_minutes | int | sum of entries |
| status | string default `draft` | state machine |
| approved_by | ulid nullable | |
| approved_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

---

## State Machine

Column: `hr_timesheets.status` — `TimesheetState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `submitted` | employee (own) | entries locked |
| `submitted` | `approved` | `hr.time.approve` (manager) | fires `TimesheetApproved` |
| `submitted` | `rejected` | `hr.time.approve` | back to employee with note, entries unlocked |
| `rejected` | `submitted` | employee (own) | |

Approver ≠ owner. Audited.

---

## DTOs

### LogTimeEntryData — date (required, not future *(assumed)*), clock_in/clock_out (required, out after in), break_minutes (min:0, less than span), notes
### SubmitTimesheetData — week_start (required); cross-field: all days closed (no running timers)
### TimesheetData (output) — id, employee_name, week_start, total_minutes, total_hours_formatted, overtime_minutes, status, entries[]

## Services & Actions

Interface→Service: `TimeServiceInterface` → `TimeService`.

- `clockIn(string $employeeId): TimeEntryData` — throws `AlreadyClockedInException`
- `clockOut(string $employeeId): TimeEntryData` — computes totals + overtime flag
- `logEntry(LogTimeEntryData $data): TimeEntryData` — manual entry
- `submitWeek(SubmitTimesheetData $data): TimesheetData`
- `approve(string $timesheetId): TimesheetData` — fires `TimesheetApproved`; throws own-approval + state exceptions
- `reject(string $timesheetId, string $note): TimesheetData`

## Events

### Fires: TimesheetApproved
| Payload field | Type |
|---|---|
| company_id | string |
| timesheet_id | string |
| employee_id | string |
| period_start | CarbonImmutable |
| period_end | CarbonImmutable |
| total_minutes | int |

(Contract in [[architecture/event-bus]].)

---

## Filament

**Nav group:** Leave

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `TimesheetResource` | #1 CRUD resource | pending-approval tab; approve/reject actions |
| `TimeEntryResource` | #1 CRUD resource | entries list, filters by employee/date |
| Clock widget (self-service dashboard) | #6 widget | clock in/out button + running timer |

---

## Permissions

`hr.time.view-any` · `hr.time.view` · `hr.time.log-own` · `hr.time.submit-own` · `hr.time.approve` · `hr.time.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Own-data: employee logs/views own entries only
- [ ] Double clock-in rejected
- [ ] Overtime flagged past standard workday
- [ ] Submit locks entries; reject unlocks
- [ ] Approve fires `TimesheetApproved` with contract payload; approver ≠ owner
- [ ] Week with running timer cannot be submitted
- [ ] Minutes math exact (no float hours)

---

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

---

## Related

- [[domains/hr/payroll]]
- [[domains/hr/shift-scheduling]]
- [[architecture/event-bus]]
