---
type: module
domain: Core Platform
panel: app
module-key: core.audit
status: planned
color: "#4ADE80"
---

# Audit Log

Full activity trail across all FlowFlex domains via Spatie Activity Log. Every write operation in every domain routes through `AuditLogger::log()` — no domain writes directly to the log table.

---

## Core Features

- `AuditLogger::log(event, subject, causer, properties)` — single entry point for all audit records
- Spatie Activity Log records: who did what, to which record, when, from which IP
- Filterable by: domain, action type, user, date range, subject model
- `rmsramos/activitylog` Filament resource — browse logs in `/app` panel
- Admin staff can view audit logs across all companies in `/admin`
- Log retention: configurable per company (default: 2 years)
- All domain events auto-logged when dispatched through `AuditLogger`

---

## Data Model

Spatie Activity Log table — no custom tables needed:

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `log_name` | string | Domain name (e.g. `hr`, `finance`) |
| `description` | string | Human-readable action |
| `subject_type` | string | Eloquent model class |
| `subject_id` | ulid | Model ID |
| `causer_type` | string | `App\Models\User` |
| `causer_id` | ulid | User who performed the action |
| `properties` | json | Before/after values, extra context |
| `company_id` | ulid | Tenant scope |
| `created_at` | timestamp | |

---

## Filament

**`/app` panel:**
- `AuditLogResource` (via `rmsramos/activitylog`) — list, filter, detail view
- Filters: date range, domain, user, action type
- No create/edit/delete — read-only

---

## Related

- [[domains/core/_index]]
- [[architecture/packages]] (`rmsramos/activitylog`)
