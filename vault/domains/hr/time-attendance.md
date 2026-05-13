---
type: module
domain: HR & People
panel: hr
module-key: hr.time
status: planned
color: "#4ADE80"
---

# Time & Attendance

> Clock-in/out, daily timesheets, overtime tracking, and attendance reporting — capturing when employees are working and flagging anomalies.

**Panel:** `hr`
**Module key:** `hr.time`

## What It Does

Time & Attendance tracks daily working hours for each employee. Employees clock in and clock out via the Self-Service portal or a designated time entry form. Each clock-in/out pair creates a time entry for the day. Weekly timesheets aggregate entries for manager review. Overtime is calculated based on the employee's contracted hours and the hours recorded. Attendance reports show absence patterns, late arrivals, and overtime by department. Data feeds into Payroll for overtime pay calculation and into Leave Management to distinguish planned leave from unexplained absence.

## Features

### Core
- Clock-in and clock-out: timestamps recorded per employee per day
- Break tracking: manual break start/end or fixed break deduction configurable per company
- Timesheet view: weekly view of hours per day with totals — employee reviews and submits, manager approves
- Contracted hours: employee's contracted weekly hours stored on the employee record — used for overtime calculation
- Overtime calculation: hours above contracted hours in a week flagged as overtime

### Advanced
- Geolocation clock-in: optional — employee must be within a configured radius of an office to clock in via mobile
- QR code clock-in: physical QR code at office entrance — scan triggers clock-in without opening app
- Attendance anomaly alerts: three or more late arrivals in a week, missing clock-out, unplanned absence — notifies manager
- Flexi-time tracking: core hours policy — employees can vary start/end times within configured bounds
- Export: timesheet data to CSV for payroll processing in external systems

### AI-Powered
- Pattern detection: identify employees consistently working significantly more or fewer hours than contracted — surfaced to HR as wellbeing and compliance concern
- Auto-fill suggestion: if an employee forgets to clock out, AI suggests the probable end time based on their historical patterns

## Data Model

```erDiagram
    time_entries {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        date work_date
        timestamp clocked_in_at
        timestamp clocked_out_at
        integer break_minutes
        decimal total_hours
        decimal overtime_hours
        string source
        string status
        timestamps created_at/updated_at
    }

    timesheets {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        date week_start
        string status
        ulid approved_by FK
        timestamp approved_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `source` | web / mobile / qr / manual |
| `status` | pending / approved / flagged |
| `timesheets.status` | draft / submitted / approved / rejected |

## Permissions

- `hr.time.clock-in-out`
- `hr.time.view-own`
- `hr.time.view-team`
- `hr.time.approve-timesheet`
- `hr.time.manage-settings`

## Filament

- **Resource:** `TimeEntryResource`, `TimesheetResource`
- **Pages:** `ListTimeEntries`, `ListTimesheets`, `ViewTimesheet`
- **Custom pages:** None
- **Widgets:** `AttendanceWidget` — today's clock-in status and this-week hours on HR dashboard
- **Nav group:** Leave (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| BambooHR Time Tracking | Employee time and attendance |
| Personio Attendance | Attendance tracking and management |
| Deputy | Shift and attendance management |
| TSheets (QuickBooks) | Time tracking and timesheets |

## Related

- [[employee-profiles]]
- [[leave-management]]
- [[payroll]]
- [[shift-scheduling]]
- [[employee-self-service]]
