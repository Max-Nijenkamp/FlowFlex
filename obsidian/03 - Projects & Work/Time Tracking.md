---
tags: [flowflex, domain/projects, time-tracking, timesheets, phase/2]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-06
---

# Time Tracking

One-click or manual time logging. Feeds automatically to payroll and client billing without manual reconciliation.

**Who uses it:** All employees, contractors, managers
**Filament Panel:** `projects`
**Depends on:** [[Task Management]] (optional — time can be logged independently)
**Phase:** 2
**Build complexity:** High — 2 resources, 2 pages, 3 tables

## Events Fired

- `TimeEntryCreated`
- `TimeEntryApproved` → consumed by [[Payroll]] (add to pay run), [[Client Billing & Retainers]] (mark billable)
- `TimeEntryRejected`

## Events Consumed

- `ClockOut` (from [[Scheduling & Shifts]]) → creates a time entry automatically

## Features

- One-click timer (start/stop, shows elapsed time in browser tab)
- Manual time entry (date, hours, minutes, description)
- Tag to: project, task, client (from CRM), internal category
- Billable vs non-billable flag
- Weekly timesheet view (grid: Mon–Sun × projects)
- Time entry bulk import (CSV for legacy data)
- Timesheet submission and approval flow (employee submits week → manager approves)
- Overtime calculation (based on contracted hours vs logged)
- Rounding rules (round to nearest 15min, 30min, or log exactly)

## Reports

- By employee
- By project
- By client
- By date range
- Billable vs non-billable breakdown
- Overtime summary

## Integration Points

When [[Payroll]] is active:
- Approved time entries for hourly workers feed into the next pay run automatically

When [[Client Billing & Retainers]] is active:
- Approved billable time entries become available for invoice generation

When [[Scheduling & Shifts]] is active:
- Clock-out creates a time entry with the shift's project/department assignment

## Database Tables (3)

1. `time_entries` — individual time log records
2. `timesheets` — weekly timesheet submission records
3. `timesheet_approvals` — approval status per timesheet per manager

## Related

- [[Projects Overview]]
- [[Task Management]]
- [[Payroll]]
- [[Client Billing & Retainers]]
- [[Scheduling & Shifts]]
- [[Invoicing]]
