---
domain: operations
module: warehouses
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Warehouses — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.warehouses.view-any` | List warehouses + transfer history |
| `operations.warehouses.manage` | Create / edit / delete warehouses, set default |
| `operations.warehouses.transfer` | Execute a stock transfer between warehouses |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('operations.warehouses.view-any')
           && BillingService::hasModule('operations.warehouses')
```

Per [[../../../architecture/filament-patterns]] #1.

---

## Tenant Isolation

- Both tables carry `company_id` with a global `CompanyScope`.
- Company A cannot view or transfer into/out of Company B's warehouses.
- Transfers run through `StockService` under `CompanyContext` — no side-door into `ops_stock_levels`.

## Data Ownership

Writes only `ops_warehouses`, `ops_warehouse_transfers`. Stock levels/movements belong to [[../inventory/_module|operations.inventory]] and are mutated solely via `StockService::move` ([[../../../security/data-ownership]]).

## Encrypted Fields

None.
