---
domain: projects
module: time-tracking
feature: time-report-export
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Report & CSV Export

Project time report (logged vs estimated) and billable CSV export for invoicing/payroll.

## Behaviour

- Report: total logged vs estimated per task and per assignee; billable split.
- Export: CSV of entries including the billable flag; rate-limited; feeds manual invoice lines in Finance (v1).

## UI

- **Kind**: custom-page (report) with an export action.
- **Page**: `ProjectTimeReportPage` at `/app/projects/time/report` (nav group Time).
- **Layout**: per-task/per-assignee logged-vs-estimate table, billable/non-billable totals, filters (project/date), export button.
- **Key interactions**: filter → recompute; export CSV (throttled) → download.
- **States**: empty (no entries in range) · loading · error (export throttled → toast) · selected (n/a).
- **Gating**: `projects.time.view-any` to view; `projects.time.export` to export.

## Data

- Owns / writes: nothing (read-only over `proj_time_entries` + read of `proj_tasks` estimates).
- Reads: entries + task estimates.
- Cross-domain writes: none — billable hours reach finance.invoicing via CSV (v1), never a `fin_*` write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: billable-hours CSV → finance.invoicing (manual lines, v1); automated integration deferred *(assumed)*.
- Shared entity: `proj_tasks` (estimates).

## Unknowns

- Automated billing integration (event vs read API); where billing rates live — see [[../unknowns]].

## Related

- [[../_module|Time Tracking]] · [[../../../finance/invoicing/_module|Invoicing]] · [[timesheet-approval|Timesheet & Approval]]
