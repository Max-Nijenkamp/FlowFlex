---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.time
status: planned
priority: p2
depends-on: [projects.tasks, core.billing, core.rbac]
soft-depends: [finance.invoicing]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [proj_time_entries]
permission-prefix: projects.time
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Time Tracking

Log time against tasks and projects. Manual entry or timer-based. Feeds into project cost reporting and billable hours for invoicing.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/tasks\|projects.tasks]] | entries log against tasks/projects |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/finance/invoicing\|finance.invoicing]] | billable hours → invoice lines (**v1: CSV export → manual invoice lines; automated integration = later ADR** *(assumed)*) |

(Distinct from `hr.time` — that's attendance/payroll; this is project effort. No data sharing.)

---

## Core Features

- Time entry: task, project, description, date, hours logged (manual) or start/stop timer
- Timer: start/stop timer on any task from task view or Kanban card; one running timer per user
- Weekly timesheet view: all entries for the week, per-user total
- Time approval: manager reviews and approves team entries (week-level approve-all *(assumed)*)
- Billable flag: mark time entries as billable or non-billable
- Project time report: total logged vs estimated per task, per assignee
- Export time entries to CSV for client billing or payroll

---

## Data Model

### proj_time_entries

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| task_id | ulid | nullable FK | project-level entries allowed *(assumed)* |
| project_id | ulid | not null FK | |
| user_id | ulid | not null FK | |
| description | string | nullable | |
| date | date | not null, not future | |
| minutes_logged | int | > 0 | **minutes int, not decimal hours** *(was hours decimal in v1 spec — minutes per convention)* |
| is_billable | boolean | default false | |
| timer_started_at | timestamp | nullable | running timer marker |
| approved_by / approved_at | ulid / timestamp | nullable | |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, user_id, date)`, `(company_id, project_id, date)`, partial: one row per user where `timer_started_at` not null *(assumed: enforced in service)*

---

## DTOs

### LogTimeData — project_id (member), task_id? (in project), date (≤ today), minutes_logged (min:1), description?, is_billable
### StartTimerData — task_id (project member)

## Services & Actions

Actions:
- `StartTimer::run(StartTimerData)` — throws `TimerAlreadyRunningException` (one per user)
- `StopTimer::run(): TimeEntryData` — computes minutes, creates entry
- `LogTimeAction` / `ApproveWeekAction::run(userId, weekStart)` — approver ≠ owner
- `ProjectTimeReportQuery::for(projectId)` — logged vs estimated per task/assignee

---

## Filament

**Nav group:** Time

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `TimeEntryResource` | #1 CRUD resource | filters: project/user/date/billable; approve action |
| `TimesheetPage` | #9 report custom page | weekly grid users × days |
| `ProjectTimeReportPage` | #9 report custom page | logged vs estimate, billable split, CSV export |
| Timer widget | on task view + kanban card | start/stop |

---

## Permissions

`projects.time.log-own` · `projects.time.view-any` · `projects.time.approve` · `projects.time.export`

---

## Test Checklist

- [ ] Tenant isolation + module gating + own-data scoping
- [ ] Second concurrent timer rejected
- [ ] Stop timer computes minutes correctly
- [ ] Future-dated entry rejected
- [ ] Approve week stamps all entries; approver ≠ owner
- [ ] Report logged-vs-estimate math (minutes) over fixtures
- [ ] CSV export includes billable flag

---

## Build Manifest

```
database/migrations/xxxx_create_proj_time_entries_table.php
app/Models/Projects/TimeEntry.php
app/Data/Projects/{LogTimeData,StartTimerData,TimeEntryData}.php
app/Actions/Projects/{StartTimer,StopTimer,LogTimeAction,ApproveWeekAction}.php
app/Exceptions/Projects/TimerAlreadyRunningException.php
app/Support/Projects/ProjectTimeReportQuery.php
app/Filament/Projects/Resources/TimeEntryResource.php
app/Filament/Projects/Pages/{TimesheetPage,ProjectTimeReportPage}.php
database/factories/Projects/TimeEntryFactory.php
tests/Feature/Projects/{TimeTrackingTest,TimerTest}.php
```

---

## Related

- [[domains/projects/tasks]]
- [[domains/projects/projects]]
- [[domains/finance/invoicing]]
- [[domains/hr/time-attendance]] — attendance, separate concern
