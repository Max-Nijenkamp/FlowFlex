---
domain: core
module: audit-log
type: module
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-04
---

# Audit Log

Full activity trail across all FlowFlex domains via Spatie Activity Log. Every write operation in every domain routes through `AuditLogger::log()` — no domain writes directly to the log table. Always-free core module.

## Module-key

`core.audit`

**Priority:** v1-core  
**Panel:** app + admin  
**Permission prefix:** `core.audit`  
**Tables:** `activity_log`  
**Events:** fires none · consumes none

## Sibling notes

- [[architecture]] — `AuditLogger` service, state-transition auto-logging, prune command + flow diagram
- [[data-model]] — `activity_log` table + ERD
- [[security]] — permissions, PII denylist, cross-company admin view
- [[unknowns]] — UNVERIFIED / `*(assumed)*` items
- Features: [[features/audit-logger]] · [[features/log-browser]] · [[features/pii-denylist]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../foundation/filament-panels/_module]] | log browser in `/app` + `/admin` |
| Hard | [[../../foundation/multi-tenancy-layer/_module]] | `company_id` on every log row |

> [!warning] UNVERIFIED — needs confirmation: exact folder slugs for the foundation panel/tenancy modules (linked by convention; confirm they resolve).

## Core Features

- `AuditLogger::log(event, subject, causer, properties)` — single entry point for all audit records
- Spatie Activity Log records: who did what, to which record, when, from which IP
- State-machine transitions auto-logged via the transition base class ([[../../../architecture/patterns/states]])
- Filterable by: domain, action type, user, date range, subject model
- `rmsramos/activitylog` Filament resource — browse logs in `/app` panel
- Admin staff can view audit logs across all companies in `/admin` (scope bypass, admin guard only)
- Log retention: configurable per company (default 2 years — pruned per [[../../../architecture/data-lifecycle]])
- **PII rule**: `properties` never contain raw values of encrypted/PII fields — field names only ([[../../../architecture/data-lifecycle]])

## Test Checklist

- [x] Tenant isolation: company A logs invisible to company B
- [x] Module gating: `AuditLogResource` hidden when `core.audit` inactive
- [x] `AuditLogger::log` sets `company_id` from context automatically
- [x] Encrypted/PII field values never appear in `properties` (denylist test)
- [x] State transition writes a `state-transition` log row with from/to
- [x] Read-only resource: no create/edit/delete actions exposed
- [x] Prune command respects per-company retention setting

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_activity_log_table.php (published + company_id column)
app/Support/Services/AuditLogger.php
app/Console/Commands/PruneAuditLogCommand.php
app/Filament/App/Resources/AuditLogResource.php (package-provided, configured)
tests/Feature/Core/{AuditLogTest,AuditPiiTest}.php
```

Spec listed `app/Console/Commands/Core/...`; real layout is flat (no `Core/` subdir) — corrected above.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| — | none | all domains | Fires and consumes no domain events. Every domain **calls** `AuditLogger::log(...)` in-process on its writes/state transitions; audit-log is the sole writer of `activity_log`. No domain writes the log table directly. |

Data ownership: audit-log owns and writes only `activity_log`. It reads `CompanyContext` + per-company retention settings read-only and stores polymorphic subject/causer references (owned by other domains) as type+id only. Other domains record activity by calling the `AuditLogger` service, never by writing the log table — the inverse of a cross-domain write ([[../../../security/data-ownership]]).

## Related

- [[../../../architecture/packages]] (`rmsramos/activitylog`, `spatie/laravel-activitylog`)
- [[../../../architecture/patterns/states]] — transition auditing
- [[../../../architecture/data-lifecycle]] — retention + PII rule
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../glossary]]
