---
domain: customer-success
module: health-scores
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Health Scores — Security

## Permissions

| Permission | Description |
|---|---|
| `cs.health.view-any` | List health scores and view the health dashboard |
| `cs.health.configure` | Edit factor weights and tier thresholds |

Seeded in `PermissionSeeder`.

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('cs.health.view-any')
           && BillingService::hasModule('cs.health')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages must state this explicitly. The `HealthDashboardPage` configuration form additionally requires `cs.health.configure`.

---

## Tenant Isolation

- Both tables carry `company_id` with a global `CompanyScope` — see [[../../../architecture/multi-tenancy]].
- `cs_health_config` is unique per `company_id` (one config per tenant).
- Signal reads (CRM accounts, support metrics, finance payment status, NPS) are executed through the owning domain's tenant-scoped read API and must never leak cross-company records.
- The recalc job runs under `WithCompanyContext`, iterating one company at a time.

---

## Rate Limiting

Not applicable. No public or portal endpoints; the only mutating surface is the internal configuration form gated by `cs.health.configure`.

---

## Encrypted Fields

None. Scores and factor breakdowns are derived numeric aggregates, not sensitive personal data.
