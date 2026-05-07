---
tags: [flowflex, domain/analytics, data-warehouse, enterprise, phase/6]
domain: Analytics
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-07
---

# Data Warehouse & Export

Enterprise data export to external BI tools. For companies that need raw data access in BigQuery, Snowflake, or S3.

**Who uses it:** Data engineers, analysts (Enterprise tier only)
**Filament Panel:** `analytics`
**Depends on:** All domain tables, File Storage
**Phase:** 6 (Enterprise & Scale)
**Build complexity:** High — 2 resources, 1 page, 2 tables

> Enterprise-tier feature. Only available to companies on the Enterprise plan. Gated by `ModuleSeeder` and plan check.

---

## Features

- **BigQuery export** — scheduled export of selected domain tables to a Google BigQuery dataset; credentials stored encrypted; schema auto-generated from `export_schemas`
- **Snowflake connector** — write domain table snapshots to a Snowflake database via JDBC/REST; configurable schedule and schema
- **S3 / CSV export** — dump any enabled domain table as Parquet or CSV to a customer-supplied S3 bucket on schedule; useful for custom ETL pipelines
- **Schema registry** — `export_schemas` table defines which domain tables and columns are eligible for export; admins enable/disable per table
- **Configurable export cadence** — each `export_jobs` record has its own schedule: hourly/daily/weekly; uses Laravel scheduler
- **Incremental export** — export only rows changed since `last_run_at` using `updated_at` watermark; reduces data transfer costs
- **Export run history** — each run logs `rows_exported`, `status`, `error_message`, and `synced_at`; queryable in the analytics panel
- **Column-level PII exclusion** — mark specific columns in `export_schemas` as PII-excluded; they are omitted from all exports without code changes
- **Data retention policy** — configure how many months of history to export; older data excluded from incremental runs
- **Connection health check** — test button validates credentials and connectivity for each export destination before saving

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `export_jobs`
| Column | Type | Notes |
|---|---|---|
| `name` | string | display name |
| `type` | enum | `bigquery`, `snowflake`, `s3`, `csv` |
| `config` | json (encrypted) | credentials and destination config — encrypted cast |
| `status` | enum | `active`, `paused`, `error` |
| `schedule_cron` | string nullable | e.g. "0 2 * * *" |
| `last_run_at` | timestamp nullable | |
| `next_run_at` | timestamp nullable | |
| `rows_exported` | bigint default 0 | |
| `error_message` | text nullable | |
| `retention_months` | integer nullable | |

### `export_schemas`
| Column | Type | Notes |
|---|---|---|
| `domain` | string | e.g. "hr", "finance" |
| `table_name` | string | e.g. "employees" |
| `columns` | json | array of {name, type, pii: bool} |
| `is_enabled` | boolean default false | |
| `export_job_id` | ulid FK nullable | → export_jobs |

---

## Events Fired

None — export runs are triggered by the scheduler, not by domain events.

---

## Events Consumed

None.

---

## Permissions

```
analytics.export-jobs.view
analytics.export-jobs.create
analytics.export-jobs.edit
analytics.export-jobs.delete
analytics.export-jobs.run
analytics.export-schemas.view
analytics.export-schemas.edit
```

---

## Related

- [[Analytics Overview]]
- [[Report Builder]]
- [[Audit Log & Activity Trail]]
