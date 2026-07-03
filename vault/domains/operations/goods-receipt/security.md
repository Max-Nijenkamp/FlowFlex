---
domain: operations
module: goods-receipt
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Goods Receipt — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.goods-receipt.view-any` | List + view GRNs |
| `operations.goods-receipt.create` | Record a goods receipt against a PO (accept/reject lines) |

Seeded in `PermissionSeeder`.

Receiving is create-only — there is no separate approve/void transition, and quality-check accept/reject happens inside the single `create` action. `create` is therefore the only command verb.

---

## Rate Limiting

| Action | Limiter | Why |
|---|---|---|
| `ReceiveGoodsPage` submit (`GrnService::receive`) | `panel-action` | Mutates inventory (posts stock `in` movements) and fires a cross-domain event per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]] |

Limiter registry: [[../../../architecture/security]].

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
