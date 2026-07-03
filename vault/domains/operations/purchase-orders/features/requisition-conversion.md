---
domain: operations
module: purchase-orders
feature: requisition-conversion
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Requisition Conversion

Turn an approved procurement requisition into a purchase order.

## Behaviour

- `PurchaseOrderService::createFromRequisition(requisitionId): PoData` reads an approved requisition (procurement domain) and creates a draft PO with lines prefilled (item, qty), cost defaulting from the supplier catalogue.
- `requisition_id` is stored on the PO for traceability (procurement origin).
- Soft dependency — hidden/disabled when the procurement module is inactive.

## UI

- **Kind**: simple-resource — a "Create PO from requisition" action surfaced on `PurchaseOrderResource` (and/or from the procurement requisition view). Not a separate page.
- **Page**: action on `PurchaseOrderResource` / requisition view at `/operations/purchase-orders`.
- **Layout**: action opens the PO create form prefilled from the requisition (supplier, lines); user reviews + sends.
- **Key interactions**: pick an approved requisition → PO form prefilled → adjust → save draft → send.
- **States**: empty (no approved requisitions → action hidden) · loading (prefill spinner) · error (requisition not approved → rejected) · selected (requisition chosen, form prefilled).
- **Gating**: `operations.purchase-orders.create` (+ requires procurement module active).

## Data

- Owns / writes: `ops_purchase_orders` (+ `requisition_id`), `ops_po_lines`.
- Reads: procurement requisition via that module's service/read API (read-only).
- Cross-domain writes: none — the requisition is read, never written; marking it "converted" is procurement's own responsibility via event/service ([[../../../../security/data-ownership]]).

## Relations

- Consumes: reads approved requisition data from [[../../../procurement/requisitions/_module|procurement.requisitions]] (read API).
- Feeds: `requisition_id` back-reference; procurement may observe PO creation to close the requisition (its own write).
- Shared entity: requisition owned by procurement.

## Test Checklist

### Unit
- [ ] Prefill maps requisition lines (item, qty) to PO lines; cost defaults from catalogue

### Feature (Pest)
- [ ] `createFromRequisition` on an approved requisition creates a draft PO with `requisition_id` set
- [ ] A non-approved requisition is rejected (no PO created)
- [ ] The requisition is only read, never written by this module (procurement closes it via its own path)

### Livewire
- [ ] Create-from-requisition action hidden when the procurement module is inactive
- [ ] Action denied without `operations.purchase-orders.create`

## Related

- [[../_module|Purchase Orders]] · [[../../../procurement/requisitions/_module|procurement.requisitions]]
