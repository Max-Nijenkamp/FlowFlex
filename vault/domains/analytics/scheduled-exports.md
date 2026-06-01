---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.exports
status: planned
color: "#4ADE80"
---

# Scheduled Exports

Schedule reports and dashboards to be generated and emailed automatically on a recurring basis.

## Core Features

- Schedule: report/dashboard, frequency (daily/weekly/monthly), recipients, format (Excel/PDF)
- Recurring generation via scheduled queue job
- Email delivery with file attached
- Delivery history log
- Pause/resume schedules
- Multiple recipients per schedule
- Timezone-aware send times (company timezone)

## Data Model

| Table | Key Columns |
|---|---|
| `bi_export_schedules` | company_id, report_id or dashboard_id, frequency, recipients (json), format, next_run_at, is_active |
| `bi_export_log` | schedule_id, company_id, generated_at, status, file_path |

## Filament

**Nav group:** Reports

- `ScheduledExportResource` — create, edit schedules; delivery history

## Cross-Domain / Jobs

- Scheduled job generates and emails exports (see [[architecture/queue-jobs]], [[architecture/email]])
- Generation uses `maatwebsite/laravel-excel` or `spatie/laravel-pdf`

## Related

- [[domains/analytics/report-builder]]
- [[domains/analytics/dashboards]]
- [[architecture/queue-jobs]]
