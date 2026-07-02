---
domain: projects
module: time-tracking
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Time Tracking — API / DTOs

## Input DTOs

### LogTimeData
`project_id` (member), `task_id?` (in project), `date` (≤ today), `minutes_logged` (min:1), `description?`, `is_billable`.

### StartTimerData
`task_id` (project member).

## Output

### TimeEntryData
`id, project_id, task_id, date, minutes_logged, is_billable, approved, user_name`.

## Export

CSV export of entries (billable flag included) via `ProjectTimeReportPage` / `TimeEntryResource` bulk action. Rate-limited per user/company.

## Public / Portal Endpoints

None.
