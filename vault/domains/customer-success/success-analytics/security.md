---
domain: customer-success
module: success-analytics
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Success Analytics — Security

## Permissions

| Permission | Description |
|---|---|
| `cs.analytics.view` | View the CS dashboard and export reports |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('cs.analytics.view')
           && BillingService::hasModule('cs.analytics')
```

Per [[../../../architecture/filament-patterns]] #1 — the custom dashboard page states this explicitly.

---

## Tenant Isolation

- The module owns no tables; every aggregation query runs through another module's tenant-scoped read API under the current `CompanyContext`. There is no side-door that could read cross-company data.
- Cached metric keys are namespaced by `company:{id}` so one tenant never reads another's cached aggregates.

---

## Rate Limiting

- **Export action (medium)** — a per-user throttle on the report export prevents repeated heavy aggregation exports ([[../../../architecture/security]]). No public/portal endpoints otherwise.

---

## Encrypted Fields

None. All outputs are derived aggregate metrics; the module stores nothing.

---

## Data-Ownership Note

This module is the clean reference for [[../../../security/data-ownership]]: it **reads broadly and writes nothing**. Any future requirement to persist a metric snapshot must create a table it owns — it must never write another domain's tables.
