---
domain: hr
module: time-attendance
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# API — Time & Attendance

Planned DTOs, services, and events. Nothing implemented yet.

## DTOs

- **LogTimeEntryData** — date (required, not future *(assumed)*), clock_in/clock_out (required, out after in), break_minutes (min:0, less than span), notes
- **SubmitTimesheetData** — week_start (required); cross-field: all days closed (no running timers)
- **TimesheetData** (output) — id, employee_name, week_start, total_minutes, total_hours_formatted, overtime_minutes, status, entries[]
- **TimeEntryData** (output) — returned by clock/log operations

## Services

Interface→Service: `TimeServiceInterface` → `TimeService` (see [[architecture]] for signatures).

## Events

### Fires: TimesheetApproved

Fired on `submitted → approved`. Intended consumer: [[../payroll/_module|hr.payroll]] for hourly pay calculation. Without payroll built, the event fires unconsumed. Contract in [[../../../architecture/event-bus]].

| Payload field | Type |
|---|---|
| company_id | string |
| timesheet_id | string |
| employee_id | string |
| period_start | CarbonImmutable |
| period_end | CarbonImmutable |
| total_minutes | int |

### Consumes

None.

## Related

- [[architecture]]
- [[../payroll/_module]]
- [[../../../architecture/event-bus]]
- [[_module]]
