---
domain: procurement
module: requisitions
feature: convert-to-po
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Convert to Purchase Order

An approved requisition becomes a purchase order in Operations. This module only records the link.

## Behaviour

- Available only on `approved` requisitions with `procurement.requisitions.convert`.
- Calls `PurchaseOrderService::createFromRequisition(requisition)` in Operations — **Operations creates the PO row**.
- On success: requisition → `converted_to_po`, `po_id` set on the requisition.
- Double conversion rejected (idempotency on `po_id`).
- If `operations.purchase-orders` is inactive, the requisition simply ends at `approved` (soft dep).

## UI

- **Kind**: simple-resource (a row/table action on `RequisitionResource`, not a page).
- **Page**: none — "Convert to PO" action on approved rows.
- **Layout**: confirm modal summarising lines → on confirm, links to the created PO.
- **Key interactions**: click convert → confirm → optimistic status change → link to PO.
- **States**: hidden (not approved / no convert perm / ops PO inactive) · loading (spinner during creation) · error (toast, status unchanged) · done (status = converted, PO link shown).
- **Gating**: `procurement.requisitions.convert`.

## Data

- Owns / writes: `proc_requisitions.status`, `proc_requisitions.po_id` — its own columns only.
- Reads: nothing extra.
- Cross-domain writes: **none** — the PO is created by Operations' own service; this module never writes `ops_purchase_orders` ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `PurchaseOrderService::createFromRequisition` (Operations) creates the PO; procurement's [[../../purchase-orders/_module|PO layer]] then adds sourcing/approval.
- Consumes: PO id returned by Operations.

## Unknowns

- Partial/line-level conversion. **UNVERIFIED** — v1 is whole-requisition. 

## Related

- [[../_module|Requisitions]] · [[../../purchase-orders/_module]] · [[../../operations/purchase-orders/_module]]
