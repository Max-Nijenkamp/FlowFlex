---
domain: it
module: asset-inventory
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Asset Inventory — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../architecture/filament-patterns]], [[../../../architecture/security]].

---

## Permissions

| Permission | Description |
|---|---|
| `it.assets.view-any` | View the asset inventory |
| `it.assets.manage` | Create, edit, delete assets |
| `it.assets.assign` | Assign / return assets |
| `it.assets.retire` | Retire assets |

Verb-per-command: `assign` covers the `in_stock → assigned` and return (`assigned → in_stock`)
transitions; `retire` covers `→ retired`. The offboard-return flag and warranty alert are
system-triggered (listener / scheduled command), no user command. Seeded in `PermissionSeeder`.

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('it.assets.view-any')
           && BillingService::hasModule('it.assets')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages must state `canAccess()` explicitly.

Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Tenant Isolation

- All queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope`
- `AssignAssetData` / `ReturnAssetData` validate that `asset_id` and `employee_id` belong to the current company before the action runs
- `FlagAssetsForReturnListener` runs under `WithCompanyContext` so the queued listener resolves the correct tenant (avoids the null-team 403 family — [[../../../architecture/patterns/tenant-context-pitfalls]])

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].

---

## Rate Limiter

> [!warning] Bulk import rate limit (medium — [[../../../_archive/build-history/security-audit-2026-06-11]])
> Bulk asset import (via core.import) mutates inventory, so its in-panel trigger action is throttled by the
> named **`panel-action`** limiter *(assumed)*; the underlying import endpoint owns the primary limiter in
> core.import per [[../../../architecture/security]]. Confirm the limiter is applied when wiring the import
> path so a large upload can't be abused.
