---
tags: [flowflex, domain/projects, time-tracking, timesheets, phase/2]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: complete
last_updated: 2026-05-07
---

# Time Tracking

One-click or manual time logging. Feeds automatically to payroll and client billing without manual reconciliation.

**Who uses it:** All employees, contractors, managers
**Filament Panel:** `projects`
**Depends on:** [[Task Management]] (optional — time can be logged independently)
**Phase:** 2
**Build complexity:** High — 2 resources, 2 pages, 3 tables

## Implementation (Phase 2 — Built)

**Filament Resources:**
- `TimeEntryResource` — nav group: Time Tracking, sort: 1
- `TimesheetResource` — nav group: Time Tracking, sort: 2

**Models:** `TimeEntry`, `Timesheet`, `TimesheetApproval`

**What's live:**
- Time entry form: task (optional, BelongsTo Task), entry_date, description, minutes (with "60 = 1 hour" helper), is_billable toggle
- Time entry table: date, task title (limit 40), description, formatted duration (Xh Ym), billable icon, approved icon
- Ternary filters for billable and approval status
- `tenant_id` auto-set on create (current auth tenant) via `mutateFormDataBeforeCreate`
- Timesheet form: week_start_date picker, status select (draft/submitted/approved/rejected)
- Timesheet table: week_start_date, status badge, submitted_at
- `tenant_id` auto-set on create (current auth tenant) via `mutateFormDataBeforeCreate`

**Column note:** `timesheets` table uses `week_start_date` (single date). Week end is implicit (start + 6 days).

**Permissions enforced:** `projects.time.*`, `projects.timesheets.*`

**Not yet built (future phases):** one-click timer, bulk CSV import, overtime calculation, rounding rules, time reports by employee/project/client

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
