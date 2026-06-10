---
type: module
domain: Analytics & BI
domain-key: analytics
panel: analytics
module-key: analytics.exports
status: planned
priority: p3
depends-on: [analytics.reports, core.billing, core.rbac, foundation.queues, foundation.email]
soft-depends: [analytics.dashboards, finance.reporting]
fires-events: []
consumes-events: []
patterns: [queues, email]
tables: [bi_export_schedules, bi_export_log]
permission-prefix: analytics.exports
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Scheduled Exports

Schedule reports and dashboards to be generated and emailed automatically on a recurring basis.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/analytics/report-builder\|analytics.reports]] | reports are the primary export source |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] + [[domains/foundation/email-setup\|foundation.email]] | gating, permissions, generation + delivery |
| Soft | [[domains/analytics/dashboards\|analytics.dashboards]] (PDF snapshot), [[domains/finance/reporting\|finance.reporting]] (financial statements) | additional sources |

---

## Core Features

- Schedule: report/dashboard, frequency (daily/weekly/monthly), recipients, format (Excel/PDF)
- Recurring generation via scheduled queue job
- Email delivery with file attached (large files: temp signed link instead, > 10MB *(assumed)*)
- Delivery history log
- Pause/resume schedules
- Multiple recipients per schedule (company users only *(assumed: no external emails v1)*)
- Timezone-aware send times (company timezone)

---

## Data Model

### bi_export_schedules — id, company_id (indexed), source_type (report/dashboard/financial), source_id, frequency (daily/weekly/monthly), send_at time, recipients (jsonb user ids), format (xlsx/pdf), next_run_at, is_active
### bi_export_log — id, schedule_id FK, company_id, generated_at, status (success/failed), file_path nullable, error nullable; pruned 90 days *(assumed)*

---

## DTOs

### CreateScheduleData — source {type, id} (exists + owner-accessible), frequency (in set), send_at, recipients[] (company users, min:1), format (in set)

## Services & Actions

- `RunScheduledExportsCommand` — due schedules (`next_run_at <= now`, company-TZ computed); per-schedule try/catch; advance `next_run_at` in same transaction; generate via report runner / pdf snapshot; mail
- `ExportScheduleService::pause/resume`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunScheduledExportsCommand` | exports | every 15 min | `next_run_at` advanced transactionally — no double sends |
| `PruneExportLogCommand` | default | daily | date guard |

---

## Filament

**Nav group:** Reports

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ScheduledExportResource` | #1 CRUD resource | pause/resume, delivery log relation |

---

## Permissions

`analytics.exports.view-any` · `analytics.exports.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Due schedule generates + mails + advances next_run_at once (re-run = no double)
- [ ] Failure logged, schedule continues next cycle
- [ ] Timezone-aware send time (TZ fixture)
- [ ] Paused schedule skipped
- [ ] Files stored tenant-scoped; large file → signed link

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

## Related

- [[domains/analytics/report-builder]]
- [[domains/analytics/dashboards]]
- [[architecture/queue-jobs]]
