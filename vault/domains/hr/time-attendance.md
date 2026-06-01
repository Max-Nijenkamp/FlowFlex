---
type: module
domain: HR & People
panel: hr
module-key: hr.time
status: planned
color: "#4ADE80"
---

# Time & Attendance

Clock-in/out, timesheet management, overtime tracking, and approval workflow. Integrates with Payroll for hourly employees.

---

## Core Features

- Clock-in/clock-out: manual entry or timer-based (in self-service portal)
- Timesheet: weekly view — employee fills in hours per day per project/task
- Overtime detection: hours > standard workday trigger overtime flag
- Manager approval of timesheets before payroll run
- Break time tracking
- Timesheet export for payroll integration
- Integration with Payroll: approved timesheet hours feed into hourly payroll calculations

---

## Data Model

| Table | Key Columns |
|---|---|
| `hr_time_entries` | company_id, employee_id, date, clock_in, clock_out, break_minutes, total_hours, notes |
| `hr_timesheets` | company_id, employee_id, week_start, total_hours, status (draft/submitted/approved), approved_by |

---

## Filament

- `TimesheetResource` — weekly timesheet view per employee, approve/reject action
- `TimeEntryResource` — individual clock entries list

---

## Related

- [[domains/hr/payroll]]
- [[domains/hr/shift-scheduling]]
