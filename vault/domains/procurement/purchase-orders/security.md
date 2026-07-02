---
domain: procurement
module: purchase-orders
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Procurement PO Layer — Security

## Access contract

`canAccess() = Auth::user()->can('procurement.purchase-orders.view-any') && BillingService::hasModule('procurement.purchase-orders')` — [[../../../architecture/filament-patterns]] #1.

## Permissions

| Permission | Grants |
|---|---|
| `procurement.purchase-orders.view-any` | see procurement POs + sourcing |
| `procurement.purchase-orders.source` | add/compare/select quotes |
| `procurement.purchase-orders.approve` | act on PO approval chain |
| `procurement.purchase-orders.view-commitments` | see committed vs actual figures |

## Authorization rules

- **Send gate is a spend control**: a PO cannot be sent until `procurement_approved_at` is set (when the module is active). This is the teeth behind PO approval.
- Quote select verifies the supplier is not blacklisted (`SupplierGate`) — no back door around the catalogue block.
- Approval acts bounded by current-level/delegate like requisitions.

## Data ownership

Writes **only** `proc_po_sourcing` and the `procurement_approved_at` column it adds to `ops_purchase_orders`. All **business** PO/line writes (create, supplier change, send) go through Operations' own service — this module calls that service, never writes `ops_po_lines` or PO business fields directly. Finance effects flow via `PurchaseApproved` to Finance's own listener. See [[../../../security/data-ownership]] + [[decisions]] (the schema-extension caveat).

## Tenancy & audit

- `proc_po_sourcing` carries `company_id` under CompanyScope — [[../../../security/tenancy-isolation]].
- Quote selection + PO approval + `procurement_approved_at` set are audited.

## Related

- [[_module]] · [[../../../security/data-ownership]] · [[api]] · [[decisions]]
