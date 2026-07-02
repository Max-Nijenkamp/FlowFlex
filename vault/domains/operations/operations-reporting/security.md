---
domain: operations
module: operations-reporting
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Operations Reporting — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.reporting.view` | View Operations dashboards + export |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('operations.reporting.view')
           && BillingService::hasModule('operations.reporting')
```

Per [[../../../architecture/filament-patterns]] #1. The dashboard custom page states this explicitly.

---

## Tenant Isolation

- Every aggregate query is `company_id`-scoped via `CompanyScope` on the underlying models.
- Cache keys are namespaced `company:{id}:…` — no cross-tenant cache bleed.

## Rate Limiting

Per [[../../../build/security-audit-2026-06-11]] (medium): throttle the Excel export endpoint per user/company (export is the expensive path).

## Data Ownership

Owns no tables; writes nothing. Reads across inventory/PO/supplier tables (owned elsewhere) for aggregation only ([[../../../security/data-ownership]]).

## Encrypted Fields

None.
