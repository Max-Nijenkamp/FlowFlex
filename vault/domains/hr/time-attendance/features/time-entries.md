---
domain: hr
module: time-attendance
feature: time-entries
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Time Entries (Clock-in/out & Manual Logging)

## Purpose

Capture worked time per employee per day, either via a timer (clock-in/out) or manual entry.

## Behavior

- Clock-in starts a running entry; clock-out closes it and computes `total_minutes` (minus `break_minutes`) and the overtime flag.
- Double clock-in is rejected (`AlreadyClockedInException`).
- Manual entry via `logEntry(LogTimeEntryData)` — requires clock_out after clock_in, break less than span, date not future *(assumed)*.
- Minutes math is exact (integer minutes, never float hours).
- Break time is tracked per entry.

## Tables / Permissions / Events

- Tables: `hr_time_entries`
- Permissions: `hr.time.log-own`, `hr.time.view`, `hr.time.view-any`, `hr.time.manage`
- Events: none (entries roll up into a timesheet)
- Surfaces: `TimeEntryResource`, self-service Clock widget

## UI

- **Kind**: simple-resource (`TimeEntryResource`) + self-service Clock widget
- **Page**: "Time Entries" (`/hr/time-entries`)
- **Layout**: table — employee, date, clock-in/out, break, total minutes, overtime badge; filter by employee/date; manual create/edit form. Companion Clock widget on the self-service dashboard shows a clock-in/out button and a running timer.
- **Key interactions**: clock in / clock out (widget); log a manual entry (`logEntry`); edit break minutes; view daily totals.
- **States**: empty ("No time logged yet — clock in to start") · loading (table/widget skeleton) · error (toast on `AlreadyClockedInException` / validation) · selected (row opens entry detail/edit).
- **Gating**: log own entries requires `hr.time.log-own`; view requires `hr.time.view`; view-any (team) requires `hr.time.view-any`; correct others requires `hr.time.manage`.

## Data

- Owns / writes: `hr_time_entries`
- Reads: reads `hr_employees` via EmployeeService (whose entry); reads company settings for standard workday
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none directly *(entries roll up into `hr_timesheets`; the timesheet fires `TimesheetApproved` — see [[timesheet-approval-workflow]])*
- Shared entity: `hr_employees` (read via EmployeeService)

## Test Checklist

### Unit
- [ ] `total_minutes = span − break_minutes` computed in integer minutes (never float hours)
- [ ] `logEntry` validation: clock_out after clock_in, break < span, date not future *(assumed)*

### Feature (Pest)
- [ ] Double clock-in throws `AlreadyClockedInException`; clock-out computes totals + overtime flag
- [ ] Company A cannot see or edit company B time entries

### Livewire
- [ ] Logging own entry requires `hr.time.log-own`; correcting another employee's entry requires `hr.time.manage`
- [ ] Clock widget in/out toggles the running timer

## Related

- [[../_module]]
