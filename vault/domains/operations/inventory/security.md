---
domain: operations
module: inventory
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Inventory — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.inventory.view-any` | List items, levels, movement ledger |
| `operations.inventory.manage-items` | Create / edit / delete items |
| `operations.inventory.move-stock` | Direct stock movement (manual `move`) |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('operations.inventory.view-any')
           && BillingService::hasModule('operations.inventory')
```

Per [[../../../architecture/filament-patterns]] #1.

---

## Tenant Isolation

- All three tables carry `company_id` with a global `CompanyScope`.
- `StockService` runs under `CompanyContext` — the single write path is also the single tenant-scope choke point.
- Company A cannot read or move Company B's stock.

## Data Ownership (security control)

`ops_stock_levels` / `ops_stock_movements` are the app's canonical stock truth. **Only `StockService` writes them** — the arch test forbids any other service touching these models. This is a privilege-containment wall: a bug in PO, GRN, adjustments, or e-commerce cannot corrupt stock except through the validated `move` path ([[../../../security/data-ownership]]).

## Encrypted Fields

None.
