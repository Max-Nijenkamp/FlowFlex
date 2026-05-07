---
tags: [flowflex, domain/analytics, audit-log, compliance, phase/6]
domain: Analytics
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-07
---

# Audit Log & Activity Trail

Immutable record of every action in the platform. Who changed what, when, and from which IP — surfaced as a filterable, exportable UI on top of Spatie Activity Log.

**Who uses it:** Compliance managers, security team, admins
**Filament Panel:** `analytics`
**Depends on:** Core (Spatie Activity Log v5 — `activity_log` table)
**Phase:** 6
**Build complexity:** Low — 1 resource, 1 page, 1 table (own)

> This module does NOT create a second activity log table. It reads from the existing `activity_log` table populated by the `LogsActivity` trait on all models. It adds only the `audit_exports` table for tracking export runs.

---

## Features

- **Immutable audit log viewer** — Filament resource displaying all activity log records; read-only; no edit or delete capability
- **Who** — causer name and type (Tenant or User); impersonation context if applicable
- **What** — before and after values for changed fields; diff view showing only changed columns
- **When** — precise timestamp with timezone display
- **From where** — IP address and user agent stored in `properties` JSON of the activity log
- **Filter by user** — filter log to a specific tenant or admin user's actions
- **Filter by model / module** — filter by `subject_type` (e.g. "App\Models\Hr\Employee") or domain
- **Filter by action type** — created, updated, deleted; restore events
- **Filter by date range** — date picker; default last 30 days
- **Export for compliance** — trigger an export of filtered audit records as CSV; export tracked in `audit_exports`
- **Retention display** — display log retention policy (configured by company); note: log pruning is handled by Spatie's built-in prune command, not this module
- **Sensitive field protection** — encrypted fields are never logged by `LogsActivity` (enforced by `->logOnly([...])` whitelist); UI confirms which fields are audited per model

---

## Database Tables

> Note: The main audit data lives in Spatie's `activity_log` table (not listed here — it is a core platform table, not a module table). This module adds one supporting table only.

> This table includes standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `audit_exports`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | who requested → tenants |
| `filters` | json | the filter criteria applied to the export |
| `file_id` | ulid FK nullable | → files (S3 CSV file) |
| `row_count` | integer nullable | |
| `exported_at` | timestamp | |
| `status` | enum | `queued`, `completed`, `failed` |

---

## Events Fired

None — Audit Log is a passive reader.

---

## Events Consumed

None.

---

## Permissions

```
analytics.audit-log.view
analytics.audit-log.export
analytics.audit-exports.view
```

---

## Implementation Notes

- Uses `spatie/laravel-activitylog` v5
- Correct namespace: `Spatie\Activitylog\Models\Concerns\LogsActivity` (not root namespace — see Brain/Patterns.md)
- All module models must use `->logOnly([...])` with explicit whitelist; never `->logFillable()` or `->logAll()`
- Never include encrypted fields in the `logOnly` list

---

## Related

- [[Analytics Overview]]
- [[Security Rules]]
- [[Data Warehouse & Export]]
- [[Access & Permissions Audit]]
