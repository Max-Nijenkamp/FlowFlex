---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.audit
status: planned
priority: v1-core
depends-on: [foundation.panels, foundation.tenancy]
soft-depends: []
fires-events: []
consumes-events: []
patterns: []
tables: [activity_log]
permission-prefix: core.audit
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Audit Log

Full activity trail across all FlowFlex domains via Spatie Activity Log. Every write operation in every domain routes through `AuditLogger::log()` — no domain writes directly to the log table. Always-free core module.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/filament-panels\|foundation.panels]] | log browser in `/app` + `/admin` |
| Hard | [[domains/foundation/multi-tenancy-layer\|foundation.tenancy]] | `company_id` on every log row |

---

## Core Features

- `AuditLogger::log(event, subject, causer, properties)` — single entry point for all audit records
- Spatie Activity Log records: who did what, to which record, when, from which IP
- State-machine transitions auto-logged via the transition base class ([[architecture/patterns/states]])
- Filterable by: domain, action type, user, date range, subject model
- `rmsramos/activitylog` Filament resource — browse logs in `/app` panel
- Admin staff can view audit logs across all companies in `/admin` (scope bypass, admin guard only)
- Log retention: configurable per company (default 2 years — pruned per [[architecture/data-lifecycle]])
- **PII rule**: `properties` never contain raw values of encrypted/PII fields — field names only ([[architecture/data-lifecycle]])

---

## Data Model

Spatie Activity Log table (published migration, extended):

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| log_name | string | domain name (e.g. `hr`, `finance`, `state-transition`) |
| description | string | human-readable action |
| subject_type / subject_id | string / ulid | target model |
| causer_type / causer_id | string / ulid | acting user (null for system/jobs) |
| properties | jsonb | before/after (PII rule applies), ip *(assumed: ip captured in properties)* |
| company_id | ulid | tenant scope — added column, indexed |
| created_at | timestamp | |

**Indexes:** `(company_id, created_at)`, `(company_id, subject_type, subject_id)`

---

## DTOs

None — log rows are written via the service and rendered by the package resource.

## Services & Actions

- `AuditLogger::log(string $event, Model $subject, ?User $causer, array $properties = []): void` — wraps spatie `activity()`, force-sets `company_id` from context, strips PII keys against a denylist *(assumed: per-model `$auditExclude` list)*

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `AuditLogResource` (`/app`, via rmsramos/activitylog) | #1 CRUD (read-only) | filters: date range, domain, user, action; no create/edit/delete |
| Admin cross-company log view (`/admin`) | #1 (read-only) | `withoutGlobalScope` allowed here only |

---

## Permissions

`core.audit.view-any` · `core.audit.view`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `PruneAuditLogCommand` | default | daily 04:30 | deletes WHERE `created_at < retention cutoff` — naturally idempotent |

---

## Test Checklist

- [ ] Tenant isolation: company A logs invisible to company B
- [ ] `AuditLogger::log` sets `company_id` from context automatically
- [ ] Encrypted/PII field values never appear in `properties` (denylist test)
- [ ] State transition writes a `state-transition` log row with from/to
- [ ] Read-only resource: no create/edit/delete actions exposed
- [ ] Prune command respects per-company retention setting

---

## Build Manifest

```
database/migrations/xxxx_create_activity_log_table.php (published + company_id column)
app/Support/Services/AuditLogger.php
app/Console/Commands/Core/PruneAuditLogCommand.php
app/Filament/App/Resources/AuditLogResource.php (package-provided, configured)
tests/Feature/Core/{AuditLogTest,AuditPiiTest}.php
```

---

## Related

- [[architecture/packages]] (`rmsramos/activitylog`, `spatie/laravel-activitylog`)
- [[architecture/patterns/states]] — transition auditing
- [[architecture/data-lifecycle]] — retention + PII rule
