---
tags: [flowflex, domain/it, access, permissions-audit, phase/6]
domain: IT & Security
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-07
---

# Access & Permissions Audit

Know exactly who has access to what, and fix overprovision before it becomes a security risk. Snapshot every tenant's access at a point in time, flag anomalies, and enforce least-privilege.

**Who uses it:** IT team, security team, compliance officers
**Filament Panel:** `it`
**Depends on:** [[Roles & Permissions (RBAC)]], [[IT Asset Management]], [[SaaS Spend Management]]
**Phase:** 6
**Build complexity:** Medium — 2 resources, 2 pages, 2 tables

---

## Features

- **Access snapshot capture** — on-demand or scheduled snapshot of every tenant's current roles, permissions, IT assets, and SaaS subscriptions; stored in `access_snapshots` as JSON
- **Cross-system access map** — unified view of a tenant's access: FlowFlex RBAC roles + permissions, assigned IT assets, and SaaS seats; displayed on a per-tenant detail page
- **Overprovision alert detection** — compare tenant's role-assigned permissions against their actual permissions; flag any extras as overprovision; create `overprovision_alerts` record
- **`OverprovisionAlertRaised` event** — fires when a new alert is created; notifies IT security team for review
- **Alert resolution workflow** — overprovision alerts have status: open → resolved; resolver notes required; audit trail via LogsActivity
- **Periodic access review** — schedule an access review cycle (monthly/quarterly); system captures snapshots for all tenants and surfaces changes since the prior snapshot
- **Auto-trigger on offboarding** — `OffboardingCompleted` event triggers a full access audit for the departing tenant; creates overprovision alerts for any access not yet revoked
- **Diff view** — compare two `access_snapshots` for the same tenant to see what changed between periods
- **Bulk revoke** — from the alert list, IT can bulk-revoke FlowFlex permissions or flag SaaS seats for removal
- **Compliance-ready export** — export all snapshots and alerts for a date range as CSV for auditor evidence

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `access_snapshots`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `captured_at` | timestamp | |
| `roles` | json | array of role names |
| `permissions` | json | array of permission strings |
| `it_assets` | json | array of {id, name, type} |
| `saas_subscriptions` | json | array of {id, name} |

### `overprovision_alerts`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `resource_type` | string | e.g. "permission", "saas_subscription", "it_asset" |
| `resource_id` | string | ID of the resource in question |
| `reason` | string | why this is flagged |
| `status` | enum | `open`, `resolved` |
| `raised_at` | timestamp | |
| `resolved_at` | timestamp nullable | |
| `resolved_by` | ulid FK nullable | → tenants |
| `resolution_notes` | text nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `OverprovisionAlertRaised` | `overprovision_alert_id`, `tenant_id` | Notification to IT security team |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `OffboardingCompleted` | [[Offboarding]] | Triggers full access audit for the departing tenant; creates overprovision alerts for any remaining access |

---

## Permissions

```
it.access-snapshots.view
it.access-snapshots.create
it.overprovision-alerts.view
it.overprovision-alerts.resolve
it.access-audit.export
```

---

## Related

- [[IT Overview]]
- [[Roles & Permissions (RBAC)]]
- [[IT Asset Management]]
- [[SaaS Spend Management]]
- [[Security & Compliance]]
- [[Offboarding]]
