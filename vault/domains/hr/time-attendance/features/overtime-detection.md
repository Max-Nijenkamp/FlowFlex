---
domain: hr
module: time-attendance
feature: overtime-detection
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Overtime Detection

## Purpose

Flag worked hours beyond the standard workday so payroll can apply overtime pay.

## Behavior

- On clock-out (or manual close), hours exceeding the standard workday set `is_overtime = true` on the entry.
- Standard workday is read from company settings *(assumed: 8h default)*.
- Overtime minutes surface in `TimesheetData.overtime_minutes` for the approval view and payroll export.

## Tables / Permissions / Events

- Tables: `hr_time_entries.is_overtime`, rolled up into `hr_timesheets`
- Permissions: read via `hr.time.view` / `hr.time.view-any`
- Events: contributes to `TimesheetApproved` payload (`total_minutes`) → hourly payroll

## UI

- **Kind**: background (computed on entry close / approval — no dedicated screen)
- **Page**: none (results surface on `TimeEntryResource` overtime badge and in the timesheet approval view)
- **Layout**: no own screen; `is_overtime` renders as a badge on time-entry rows and `overtime_minutes` shows in `TimesheetData` for the approval/payroll view.
- **Key interactions**: none direct — triggered on clock-out or manual close when hours exceed the standard workday.
- **States**: n/a (no interactive UI) — surfaced read-only via entry/timesheet screens.
- **Gating**: read via `hr.time.view` / `hr.time.view-any`; no separate write permission (system-computed).

## Data

- Owns / writes: `hr_time_entries.is_overtime` (computed flag on this module's own table)
- Reads: reads company settings for standard workday *(assumed: 8h default)*
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none directly *(contributes `total_minutes`/`overtime_minutes` to the `TimesheetApproved` payload consumed by [[../../payroll/_module|hr.payroll]])*
- Shared entity: standard-workday setting (company settings)

## Related

- [[../_module]]
- [[../unknowns]]
