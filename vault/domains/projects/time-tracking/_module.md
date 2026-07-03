---
domain: projects
module: time-tracking
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Time Tracking

Log time against tasks and projects — manual entry or timer-based. Feeds project cost reporting and billable hours for invoicing.

## Module-key

`projects.time`

**Priority:** p2  
**Panel:** projects  
**Permission prefix:** `projects.time`  
**Tables:** `proj_time_entries`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tasks/_module\|projects.tasks]] | entries log against tasks/projects |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../finance/invoicing/_module\|finance.invoicing]] | billable hours → invoice lines (**v1: CSV export → manual lines; automated integration = later ADR** *(assumed)*) |

> Distinct from `hr.time-attendance` (attendance/payroll). This is project effort — no data sharing.

## Core Features

- Time entry: task, project, description, date, minutes (manual) or start/stop timer.
- Timer: one running timer per user; start/stop from task view or Kanban card.
- Weekly timesheet view (per-user totals).
- Time approval: manager approves team entries (week-level *(assumed)*).
- Billable flag; project time report (logged vs estimated); CSV export.

## See features/

- [[features/time-entry-timer|Entry & Timer]] — manual entry + one-running-timer.
- [[features/timesheet-approval|Timesheet & Approval]] — weekly grid + week-level approve.
- [[features/time-report-export|Report & CSV Export]] — logged-vs-estimate + billable export.

## Build Manifest

```
database/migrations/xxxx_create_proj_time_entries_table.php
app/Models/Projects/TimeEntry.php
app/Data/Projects/{LogTimeData,StartTimerData,TimeEntryData}.php
app/Actions/Projects/{StartTimer,StopTimer,LogTimeAction,ApproveWeekAction}.php
app/Exceptions/Projects/TimerAlreadyRunningException.php
app/Support/Projects/ProjectTimeReportQuery.php
app/Filament/Projects/Resources/TimeEntryResource.php · Pages/{TimesheetPage,ProjectTimeReportPage}.php
database/factories/Projects/TimeEntryFactory.php
tests/Feature/Projects/{TimeTrackingTest,TimerTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see/log/approve company B's time entries.
- [ ] Module gating: artifacts hidden when `projects.time` inactive.
- [ ] Own-data scoping: without `view-any`, a user sees/logs only their own entries.
- [ ] Second concurrent timer rejected.
- [ ] Stop timer computes minutes correctly.
- [ ] Future-dated entry rejected.
- [ ] Approve week stamps all entries; approver ≠ owner.
- [ ] Report logged-vs-estimate math (minutes) over fixtures.
- [ ] CSV export includes billable flag.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | task/project ownership | projects.tasks | entries target member tasks/projects |
| Feeds | billable hours (CSV, v1) | finance.invoicing | manual invoice lines; automated integration deferred *(assumed)* |
| Reads | actuals consumer | projects.projects / resources | `ProjectService::actuals`, allocation utilisation |

**Data ownership:** `projects.time` writes only `proj_time_entries`. Invoicing consumes billable hours via CSV (v1) or a future read API — never a direct write into `fin_*` tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../tasks/_module|Tasks]] · [[../projects/_module|Projects]] · [[../../finance/invoicing/_module|Invoicing]]
- [[../../../glossary]]
