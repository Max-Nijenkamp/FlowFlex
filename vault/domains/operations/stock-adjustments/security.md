---
domain: operations
module: stock-adjustments
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Stock Adjustments — Security

## Permissions

| Permission | Description |
|---|---|
| `operations.adjustments.view-any` | List adjustments + reports |
| `operations.adjustments.create` | Create an adjustment / run a stocktake |
| `operations.adjustments.approve` | Approve a pending high-value adjustment (pending-approval → applied) |

Seeded in `PermissionSeeder`.

**Verb-per-transition check:** `create` covers adjustment/stocktake creation; `approve` covers the one meaningful transition (pending-approval → applied). Under-threshold adjustments apply on create (no separate verb).

---

## Access Contract

```php
canAccess() = Auth::user()->can('operations.adjustments.view-any')
           && BillingService::hasModule('operations.adjustments')
```

Per [[../../../architecture/filament-patterns]] #1. The `StocktakePage` custom page states this explicitly.

---

## Segregation of Duties

- Above-threshold adjustments require approval; `approved_by` must differ from `adjusted_by` (self-approval blocked). This is an internal fraud/shrinkage control — write-offs and theft adjustments are the exact spots that need a second pair of eyes.
- Stock is untouched while `pending-approval`.

## Rate Limiting

| Action | Limiter | Why |
|---|---|---|
| Stocktake bulk submission (`StocktakePage` confirm) | `panel-action` | Mutates inventory (posts a movement per non-zero delta); throttles large bulk-adjustment runs ([[../../../build/security-audit-2026-06-11]], medium) |
| Write-off report export (Excel) | `exports` | Bulk export per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]] |

Limiter registry: [[../../../architecture/security]].

## Tenant Isolation & Data Ownership

- `company_id` + `CompanyScope`; company A cannot adjust company B's stock.
- Writes only `ops_stock_adjustments`; the delta is applied via `StockService::move` (same-domain). No GL write here — write-offs are reported, not posted ([[../../../security/data-ownership]]).

## Encrypted Fields

None.
