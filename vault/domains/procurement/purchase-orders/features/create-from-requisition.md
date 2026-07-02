---
domain: procurement
module: purchase-orders
feature: create-from-requisition
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Create PO from Requisition

The entry point for the procurement PO layer: an approved requisition's convert action produces an Operations PO, which this layer then decorates with sourcing/approval/commitment.

## Behaviour

- Triggered by [[../../requisitions/features/convert-to-po|requisitions convert]] → `PurchaseOrderService::createFromRequisition` (Operations creates the PO + lines).
- This layer attaches: an empty sourcing set, `procurement_approved_at = null`, and shows the PO under the Procurement nav.
- PO starts in Operations' `draft`; not sendable until procurement-approved ([[po-approval]]).

## UI

- **Kind**: simple-resource (list/detail over ops POs; creation is via the requisition action, not a manual form here).
- **Page**: "Purchase Orders" (`/operations/procurement/purchase-orders`).
- **Layout**: table — PO number, supplier, total, requisition link, procurement-approval badge, commitment badge.
- **Key interactions**: open PO → detail with sourcing tab, approval actions, commitment figures; link back to the source requisition.
- **States**: empty ("No procurement POs yet — convert an approved requisition") · loading (skeleton) · error (toast) · selected (PO detail).
- **Gating**: `procurement.purchase-orders.view-any`.

## Data

- Owns / writes: initialises `proc_po_sourcing` context + `procurement_approved_at` (own column) — nothing else.
- Reads: `ops_purchase_orders` / `ops_po_lines` (Operations, read for display).
- Cross-domain writes: **none** — the PO is created by Operations' service ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `createFromRequisition` result from Operations; requisition link from [[../../requisitions/_module|requisitions]].
- Feeds: PO into [[sourcing]] → [[po-approval]].

## Unknowns

- Whether draft POs can be created directly here without a requisition. `*(assumed: requisition-only v1)*`

## Related

- [[../_module|Procurement PO Layer]] · [[../../requisitions/features/convert-to-po]] · [[po-approval]]
