---
domain: analytics
module: scheduled-exports
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Scheduled Exports — Services & Contracts

No REST API and no domain events in v1. Interaction is: **reads** from source domains' read paths, **delivery** via the shared mail infrastructure.

---

## Services & Actions

- `RunScheduledExportsCommand` — every 15 min, selects due schedules (`next_run_at <= now`, company-TZ), generates each via its source read path, mails it, advances `next_run_at` transactionally with the log write.
- `PruneExportLogCommand` — daily; prunes `bi_export_log` older than 90 days *(assumed)*.
- `ExportScheduleService::pause() / resume()` — toggle `is_active`.
- `ScheduledExportMail` — `FlowFlexMailable` + `ShouldQueue`; attaches the file, or a tenant-scoped signed link for large files.

---

## Cross-domain contracts

| Direction | Mechanism | Counterpart |
|---|---|---|
| Read report source | `ReportRunner::run()` | [[../report-builder/_module\|analytics.reports]] |
| Read dashboard source | dashboard PDF snapshot | [[../dashboards/_module\|analytics.dashboards]] (soft) |
| Read financial source | statement read API | [[../../finance/financial-reporting/_module\|finance.reporting]] (soft) |
| Deliver | queue + mail | [[../../foundation/queue-workers/_module\|foundation.queues]], [[../../foundation/email-setup/_module\|foundation.email]] |

Each source is read through its owning domain's read path; Analytics writes only its own two tables and hands delivery to the mail infrastructure ([[../../../security/data-ownership]]).

---

## Events

None fired, none consumed.

See [[data-model]], [[security]], [[./features/recurring-generation|Recurring Generation feature]].
