---
type: module
domain: Core Platform
panel: admin
cssclasses: domain-admin
phase: 1
status: complete
migration_range: 010001–019999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# Audit Log

Immutable, searchable record of every action taken in the platform. Who did what, when, to which record, from which IP. Required for enterprise compliance, GDPR, SOX, ISO 27001, and SOC 2.

**Panel:** `admin` (super-admin view) + per-panel audit log per domain  
**Phase:** 1 — must exist before any other module records actions

> Note: "Audit Log & Activity Trail" in Analytics (Phase 6) is the cross-domain analytical view (trends, anomaly detection, export to SIEM). This module is the **core infrastructure and per-record UI** that Analytics builds on.

---

## Features

### Event Recording
- Automatic capture via `spatie/laravel-activitylog` on all Eloquent models
- Custom manual log entries via `AuditLogger::log()` for non-model actions
- Captured per event: actor (user ID + name + email), action (created/updated/deleted/restored/exported/login/logout), subject (model type + ID), changed attributes (before/after diff), IP address, user agent, timestamp
- Super-admin actions tagged separately (distinguish company admin from FlowFlex staff access)

### Search & Filter UI
- Filter by: user, date range, action type, model type, record ID
- Full-text search on description
- "View changes" — side-by-side diff of before/after values
- Export filtered results to CSV (for auditors)
- Infinite scroll or paginated list (most recent first)

### Per-Record Audit Trail
- Every Filament resource shows audit log tab on detail view
- See full history of that specific record (e.g. all changes to Invoice #1234)
- Reverting to previous value (super-admin only, with audit entry for the revert)

### Retention & Immutability
- Audit records are append-only — no update or delete via application
- Retention: 7 years minimum (configurable per company policy)
- GDPR erasure: actor fields anonymised when user is erased, but event record preserved (legal requirement)
- Long-term storage: archive to S3 after 1 year (queryable via Athena or export)

### Access Control
- `core.audit.view-own-actions` — user can view their own actions
- `core.audit.view-team` — manager views team actions
- `core.audit.view-any` — full audit access (compliance/legal role)
- `core.audit.export` — download audit data

### Critical Event Alerting
- Alert on: bulk delete (>50 records), data export (>1000 rows), permission escalation, login from new country, failed login x5
- Alert via: email + in-app notification to company owner + IT security role

---

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

No `updated_at`, no `deleted_at` — this table is immutable.

---

## Permissions

```
core.audit.view-own-actions
core.audit.view-team
core.audit.view-any
core.audit.export
```

---

## Related

- [[MOC_CorePlatform]]
- [[entity-user]]
- [[concept-multi-tenancy]]
- [[MOC_Analytics]] — Phase 6 builds cross-domain analytics on top of this data
