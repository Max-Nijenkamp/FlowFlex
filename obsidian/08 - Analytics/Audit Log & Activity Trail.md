---
tags: [flowflex, domain/analytics, audit-log, compliance, phase/5]
domain: Analytics, BI & Reporting
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-06
---

# Audit Log & Activity Trail

Immutable record of every action in the platform. Who changed what, when, from which IP.

**Who uses it:** Compliance managers, security team, admins
**Filament Panel:** `analytics`
**Depends on:** Core (Spatie Activity Log)
**Phase:** 5

## Features

- **Immutable log** — every create, update, delete on every audited model
- **Who** — user identity, impersonation context
- **What** — before and after values for changed fields
- **When** — precise timestamp
- **From where** — IP address, user agent
- **Filter** — by user, module, action type, date range
- **Export for compliance** — CSV/PDF export for auditors

## Implementation

Uses `spatie/laravel-activitylog`. All module models with `LogsActivity` trait automatically write here.

See [[Security Rules]] — auditing every write is non-optional.

## Related

- [[Analytics Overview]]
- [[Security Rules]]
- [[Data Warehouse & Export]]
