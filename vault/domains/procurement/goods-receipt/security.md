---
domain: procurement
module: goods-receipt
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# 3-Way Match — Security

## Access contract

`canAccess() = Auth::user()->can('procurement.goods-receipt.view-any') && BillingService::hasModule('procurement.goods-receipt')` — [[../../../architecture/filament-patterns]] #1.

## Permissions

| Permission | Grants |
|---|---|
| `procurement.goods-receipt.view-any` / `.view-matches` | see the match queue + variances |
| `procurement.goods-receipt.resolve` | resolve a flagged match (reject-bill) |
| `procurement.goods-receipt.override` | override-approve a discrepancy (notes required) |

## Authorization rules

- **Override is the sensitive control** — it lets a bill be paid despite a discrepancy. Gated on `override`, always requires notes, always audited. Segregation of duties: overrider should differ from the bill creator *(assumed — confirm at build)*.
- The payment gate cannot be bypassed from procurement — it's enforced inside Finance's own `approveBill`.

## Data ownership

Writes **only** `proc_three_way_matches`. Reads PO/GRN/bill from Operations + Finance. The AP payment block is enforced by Finance's service consulting this module's match state / `ThreeWayMatchResolved` event — procurement never writes `finance_ap_*`, `ops_grn*`, or `ops_purchase_orders`. See [[../../../security/data-ownership]].

## Tenancy & audit

- `proc_three_way_matches` carries `company_id` under CompanyScope — [[../../../security/tenancy-isolation]].
- Every evaluate result + resolve/override action audited (who overrode which variance and why).

## Related

- [[_module]] · [[../../../security/data-ownership]] · [[api]]
