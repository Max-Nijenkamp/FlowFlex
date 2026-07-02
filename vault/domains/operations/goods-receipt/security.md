---
domain: operations
module: goods-receipt
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Goods Receipt — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.goods-receipt.view-any` | List + view GRNs |
| `operations.goods-receipt.create` | Record a goods receipt against a PO |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('operations.goods-receipt.view-any')
           && BillingService::hasModule('operations.goods-receipt')
```

Per [[../../../architecture/filament-patterns]] #1. The `ReceiveGoodsPage` custom page states this explicitly.

---

## Tenant Isolation

- Both tables carry `company_id` with a global `CompanyScope`.
- `GoodsReceived` carries `company_id` as a scalar; the finance.ap listener runs under `WithCompanyContext`.
- Company A cannot receive against company B's POs.

## Data Ownership (security control)

The GRN transaction touches three domains' concerns but writes only its **own** two tables. Stock is written by `StockService`, PO receipts by `PurchaseOrderService` (same-domain calls); the Finance bill is written by finance.ap's **own** listener reacting to `GoodsReceived`. No direct cross-domain writes — the receipt cannot corrupt stock, PO, or finance data except through their validated paths ([[../../../security/data-ownership]]).

## Encrypted Fields

None.
