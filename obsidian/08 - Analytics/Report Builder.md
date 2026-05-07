---
tags: [flowflex, domain/analytics, reports, phase/6]
domain: Analytics
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-07
---

# Report Builder

Self-serve reporting for every team. No SQL knowledge required — pick a domain, select columns, apply filters, and schedule delivery.

**Who uses it:** All roles (view), managers and admins (build and schedule)
**Filament Panel:** `analytics`
**Depends on:** All active domain tables, File Storage
**Phase:** 6
**Build complexity:** Very High — 2 resources, 2 pages, 2 tables

---

## Features

- **Domain-based builder** — select a base domain (HR, Finance, CRM, Projects, etc.); available columns driven by that domain's data model
- **Column selection and ordering** — choose which columns to include; drag to reorder; rename column labels for display
- **Filtering** — add multiple filter conditions (field + operator + value); operators: equals, contains, greater than, in list, is null, date range
- **Sorting and grouping** — sort by any column; group rows to produce subtotals and summary rows
- **Aggregation functions** — count, sum, average, min, max per group
- **Saved report library** — save with a name and description; private (own use) or shared (team-visible)
- **Scheduled delivery** — run on cron (daily/weekly/monthly); deliver PDF or CSV to a list of email recipients stored in `schedule_recipients`
- **On-demand run** — run any saved report immediately via queue job; file stored to S3 when complete
- **`ReportGenerated` event** — fires on completion; triggers email delivery to recipients
- **Run history** — every run stored in `report_runs` with status, row count, duration, and download link
- **Cross-domain joins (limited)** — select related fields across linked tables (e.g. employee + leave requests); no arbitrary SQL
- **Export formats** — CSV and PDF; PDF formatted with company branding
- **Large report handling** — reports over 50,000 rows chunked and processed via queue jobs

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `reports`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `domain` | string | e.g. "hr", "finance", "crm" |
| `base_table` | string | e.g. "employees", "invoices" |
| `filters` | json | array of {field, operator, value} |
| `columns` | json | array of {field, label, aggregation} |
| `sort_by` | string nullable | |
| `sort_direction` | enum | `asc`, `desc` |
| `group_by` | string nullable | |
| `tenant_id` | ulid FK | owner → tenants |
| `is_shared` | boolean default false | |
| `is_scheduled` | boolean default false | |
| `schedule_cron` | string nullable | e.g. "0 8 * * 1" |
| `schedule_format` | enum | `csv`, `pdf` |
| `schedule_recipients` | json nullable | array of email addresses |
| `last_run_at` | timestamp nullable | |

### `report_runs`
| Column | Type | Notes |
|---|---|---|
| `report_id` | ulid FK | → reports |
| `tenant_id` | ulid FK nullable | who triggered → tenants |
| `status` | enum | `queued`, `running`, `completed`, `failed` |
| `file_id` | ulid FK nullable | → files (output file) |
| `row_count` | integer nullable | |
| `ran_at` | timestamp | |
| `completed_at` | timestamp nullable | |
| `duration_ms` | integer nullable | |
| `error_message` | text nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `ReportGenerated` | `report_run_id`, `file_id`, `recipients` | Email with download link to `schedule_recipients` |

---

## Events Consumed

None — triggered on schedule or on demand.

---

## Permissions

```
analytics.reports.view
analytics.reports.create
analytics.reports.edit
analytics.reports.delete
analytics.reports.run
analytics.reports.schedule
analytics.reports.share
analytics.report-runs.view
analytics.report-runs.download
```

---

## Related

- [[Analytics Overview]]
- [[Custom Dashboards]]
- [[Financial Reporting]]
- [[Data Warehouse & Export]]
