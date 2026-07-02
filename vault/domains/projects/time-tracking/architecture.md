---
domain: projects
module: time-tracking
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Time Tracking — Architecture

## Services & Actions

Actions (lorisleiva/laravel-actions):

- `StartTimer::run(StartTimerData)` — one running timer per user → `TimerAlreadyRunningException`.
- `StopTimer::run(): TimeEntryData` — computes minutes, creates the entry.
- `LogTimeAction` — manual entry.
- `ApproveWeekAction::run(userId, weekStart)` — approver ≠ owner.
- `ProjectTimeReportQuery::for(projectId)` — logged vs estimated per task/assignee.

## Units

Time stored as **minutes (int)**, not decimal hours — no float math. Money (rates) in cents where applicable.

## Events

None cross-domain. Billable hours flow to finance via CSV export (v1).

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `TimeEntryResource` | Time | #1 CRUD | filters project/user/date/billable; approve action |
| `TimesheetPage` | Time | #9 report page | weekly grid users × days |
| `ProjectTimeReportPage` | Time | #9 report page | logged vs estimate, billable split, CSV export |
| Timer widget | (task view + kanban card) | — | start/stop |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.time.view-any')
        && BillingService::hasModule('projects.time');
}
```

Own-data scope: users log/see their own time unless they hold `projects.time.view-any`.

## Jobs & Scheduling

None (export is on-demand).

## Search & Realtime

None. CSV export endpoint is rate-limited (per-user/company). See [[security]].
