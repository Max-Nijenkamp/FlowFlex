---
domain: hr
module: leave-management
feature: team-calendar
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Team Calendar & Overlap Detection

## Purpose

Monthly/weekly view of approved leave across the team, with overlap detection and public-holiday awareness.

## Behavior

- Calendar (`saade/filament-fullcalendar`) shows approved leaves across team; team filter; Livewire polling 30s (no Reverb — not collaborative).
- Overlap detection: warns (does not block) when a request overlaps existing approved leave or a public holiday.
- Public holidays imported from locale settings; excluded from `days_requested`.
- Working-day calculation excludes weekends + public holidays (`calculateWorkingDays()`, see [[../architecture]]).

## UI

- **Kind**: custom-page (calendar, `saade/filament-fullcalendar`)
- **Page**: "Team Calendar" (`/hr/leave-calendar`)
- **Layout**: `LeaveCalendarPage` month/week fullcalendar showing approved leave across the team; team filter; public holidays rendered as background events. Livewire polling 30s (no Reverb — not collaborative).
- **Key interactions**: switch month/week; filter by team; hover an event for detail; overlap warning surfaces when a new request overlaps existing approved leave or a public holiday.
- **States**: empty ("No approved leave this period") · loading (calendar skeleton) · error (inline banner, retry) · selected (event popover with employee/type/dates).
- **Gating**: visible with `hr.leave.view`; team-wide view requires `hr.leave.view-any` *(assumed)*. Custom page declares `canAccess()` explicitly.

## Data

- Owns / writes: none (read-only view over this module's `hr_leave_requests`)
- Reads: reads `hr_leave_requests` (approved), index `(company_id, start_date, end_date)`; reads `hr_employees` via EmployeeService; reads public holidays from locale settings
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none
- Shared entity: `hr_employees` (read via EmployeeService); public-holiday reference data from company locale settings

## Related

- Reads: `hr_leave_requests`, indexed `(company_id, start_date, end_date)` for calendar/overlap queries (see [[../data-model]])
- UI: `LeaveCalendarPage` (#4 calendar custom page — see [[../../../../architecture/patterns/custom-pages]])
- Exception: `OverlappingLeaveException` (only when type forbids overlap *(assumed)*)
- Tests: overlap warning on overlapping approved leave; public holidays excluded from `days_requested`
- Back to [[../_module]]
