---
type: module
domain: Core Platform
panel: app
module-key: core.audit
status: planned
color: "#4ADE80"
---

# Audit Log

> Immutable, searchable record of every action taken in the platform — who did what, when, to which record, from which IP.

**Panel:** `app`
**Module key:** `core.audit`

## What It Does

The Audit Log module captures every meaningful action taken by every user across all domains. It is powered by `spatie/laravel-activitylog` and writes to an append-only `audit_logs` table — no update or delete path exists in the application. Every Filament resource shows a per-record audit trail tab so users can see the full history of any specific record. The module is required for enterprise compliance standards including GDPR, SOX, ISO 27001, and SOC 2. The Analytics domain builds cross-domain trend and anomaly analysis on top of this data.

## Features

### Core
- Automatic capture via `spatie/laravel-activitylog` on all Eloquent models using the `LogsActivity` trait
- Captured per event: actor (user ID, name, email), action (created/updated/deleted/restored/exported/login/logout), subject (model type + ID), changed attributes (before/after diff), IP address, user agent, timestamp
- Manual log entries via `AuditLogger::log()` for non-model actions (login, export, impersonation)
- Filter UI: by user, date range, action type, model type, record ID; full-text search on description
- Side-by-side before/after diff view on any log entry

### Advanced
- Per-record audit trail tab on every Filament resource detail view — full change history for one specific record
- Export filtered results to CSV for auditors
- Retention: records are append-only; retention period configurable per company (minimum 7 years)
- GDPR erasure: actor fields anonymised when a user is erased; the event record itself is preserved (legal requirement)
- Critical event alerting: bulk delete >50 records, data export >1000 rows, permission escalation, login from new country, five consecutive failed logins — alert fires to company owner and IT security role via notification system
- Super-admin (FlowFlex staff) impersonation actions tagged separately in the log

### AI-Powered
- Anomaly detection: unusual access patterns (after-hours bulk export, login from new country) flagged automatically and surfaced in the critical event alerting pipeline

## Data Model

```erDiagram
    audit_logs {
        ulid id PK
        ulid company_id FK
        ulid actor_id FK
        string actor_type
        string action
        string subject_type
        ulid subject_id
        string description
        json old_values
        json new_values
        string ip_address
        string user_agent
        json metadata
        timestamp created_at
    }
```

| Column | Notes |
|---|---|
| `id` | ULID, append-only — no update/delete |
| `company_id` | Scopes to tenant; null for FlowFlex staff actions |
| `actor_id` | FK to users or admins depending on `actor_type` |
| `action` | created / updated / deleted / restored / exported / login / logout |
| `old_values` / `new_values` | JSON diff of changed attributes |
| `metadata` | IP address, user agent, request context |

## Permissions

- `core.audit.view-own-actions`
- `core.audit.view-team`
- `core.audit.view-any`
- `core.audit.export`
- `core.audit.configure-retention`

## Filament

- **Resource:** `AuditLogResource` — read-only list, filter panel, detail view with diff
- **Pages:** `ListAuditLogs` (filtered table), `ViewAuditLog` (diff view)
- **Custom pages:** None
- **Widgets:** None
- **Nav group:** Settings (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Datadog | Application event logging for compliance |
| Splunk | Security information and event management (SIEM) ingestion |
| Vanta | Audit trail for SOC 2 evidence |
| ManageEngine | User activity monitoring |

## Related

- [[notifications]]
- [[company-settings]]
- [[multi-tenancy-layer]]
