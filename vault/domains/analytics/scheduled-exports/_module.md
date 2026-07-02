---
domain: analytics
module: scheduled-exports
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Scheduled Exports

Schedule reports and dashboards to be generated and emailed automatically on a recurring, timezone-aware basis, with a delivery log.

> Planned for build. Any "shipped/built" language reflects the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

---

## Module-key

`analytics.exports`

**Priority:** p3
**Panel:** analytics
**Permission prefix:** `analytics.exports`
**Tables:** `bi_export_schedules`, `bi_export_log`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../report-builder/_module\|analytics.reports]] | reports are the primary export source |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../foundation/queue-workers/_module\|foundation.queues]] + [[../../foundation/email-setup/_module\|foundation.email]] | gating, permissions, generation + delivery |
| Soft | [[../dashboards/_module\|analytics.dashboards]] (PDF snapshot), [[../../finance/financial-reporting/_module\|finance.reporting]] (statements) | additional sources |

---

## Core Features

- Schedule: source (report/dashboard/financial), frequency (daily/weekly/monthly), recipients, format (Excel/PDF)
- Recurring generation via a scheduled queue command
- Email delivery with file attached (large files: temp signed link instead, > 10 MB *(assumed)*)
- Delivery history log
- Pause / resume schedules
- Multiple recipients (company users only *(assumed: no external emails v1)*)
- Timezone-aware send times (company timezone)

See feature notes: [[./features/schedule-management|Schedule Management]] · [[./features/recurring-generation|Recurring Generation]] · [[./features/delivery-log|Delivery Log]].

---

## Build Manifest

```
database/migrations/xxxx_create_bi_export_schedules_table.php
database/migrations/xxxx_create_bi_export_log_table.php
app/Models/Analytics/{ExportSchedule,ExportLog}.php
app/Data/Analytics/CreateScheduleData.php
app/Services/Analytics/ExportScheduleService.php
app/Console/Commands/Analytics/{RunScheduledExportsCommand,PruneExportLogCommand}.php
app/Mail/Analytics/ScheduledExportMail.php
app/Filament/Analytics/Resources/ScheduledExportResource.php
database/factories/Analytics/ExportScheduleFactory.php
tests/Feature/Analytics/ScheduledExportTest.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot see company B schedules, logs, or export files
- [ ] Module gating: artifacts hidden when `analytics.exports` inactive
- [ ] Due schedule generates + mails + advances `next_run_at` once (re-run = no double)
- [ ] Failure logged, schedule continues next cycle
- [ ] Timezone-aware send time (TZ fixture)
- [ ] Paused schedule skipped
- [ ] Files stored tenant-scoped; large file → signed link

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `ReportRunner::run()` (report source) | [[../report-builder/_module\|analytics.reports]] | primary export source |
| Reads | dashboard render / PDF snapshot | [[../dashboards/_module\|analytics.dashboards]] (soft) | dashboard-as-PDF source |
| Reads | financial statement read | [[../../finance/financial-reporting/_module\|finance.reporting]] (soft) | statement source |
| Uses | queue + mail infrastructure | [[../../foundation/queue-workers/_module\|foundation.queues]], [[../../foundation/email-setup/_module\|foundation.email]] | generation + delivery |

**Data ownership:** `analytics.exports` writes only `bi_export_schedules`, `bi_export_log`. It reads report/dashboard/financial sources through their own read paths, and delivers via the shared mail infrastructure — never writing another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[../report-builder/_module|analytics.reports]]
- [[../dashboards/_module|analytics.dashboards]]
- [[../../../architecture/queue-jobs]] · [[../../../security/data-ownership]]
