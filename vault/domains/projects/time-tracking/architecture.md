---
domain: projects
module: time-tracking
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Time

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `TimeEntryResource` | #1 CRUD resource | tweaks: custom-header-actions (approve week) | list filters project/user/date/billable; CSV bulk export names the `exports` rate limiter ([[security]]) |
| `TimesheetPage` | #9 report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] | weekly grid users × days; week navigator; approve-week action |
| `ProjectTimeReportPage` | #9 report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] | logged vs estimate, billable split; CSV export names the `exports` rate limiter ([[security]]) |
| Timer control | embedded Livewire component *(not a standalone page — hosted on the projects.tasks task view + projects.kanban card)* | — | start/stop one running timer per user |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.time.view-any') && BillingService::hasModule('projects.time')`
per [[../../../architecture/filament-patterns]] #1. `TimesheetPage` and `ProjectTimeReportPage` are custom pages and
MUST state this explicitly — Filament does not auto-gate custom pages. Own-data scope: without `projects.time.view-any`
a user logs/sees only their own entries (`projects.time.log-own`); the export action additionally requires
`projects.time.export`.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Time-entry CRUD (manual log / edit, `minutes_logged` int) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Timer start/stop (one running timer per user) | Pessimistic | `DB::transaction()` + `lockForUpdate()` single-running-timer guard → `TimerAlreadyRunningException` ([[../../../architecture/patterns/states]]) |
| Week approval (`ApproveWeekAction` stamps every entry) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate (approver ≠ owner), write per [[../../../architecture/patterns/states]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None (export is on-demand).

## Search & Realtime

None. CSV export endpoint is rate-limited (per-user/company). See [[security]].
