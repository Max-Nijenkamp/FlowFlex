---
domain: operations
module: suppliers
feature: supplier-performance
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Supplier Performance

On-time delivery rate and order history per supplier, derived from PO and GRN data.

## Behaviour

- `SupplierService::performance(supplierId)` computes `on_time_rate` (GRN `received_at` ≤ PO `expected_delivery`) and `order_count` over the supplier's POs.
- Read-only — nothing is stored; always reflects current PO/GRN state.
- Order history lists the supplier's POs with status + receipt progress.

## UI

- **Kind**: widget — performance + order-history panels on the supplier view page (`OpsSupplierResource`). Deeper cross-supplier comparison lives in [[../../operations-reporting/_module|operations.reporting]].
- **Page**: performance panel on `OpsSupplierResource` view at `/operations/suppliers/{id}`.
- **Layout**: stat cards (on-time %, order count, avg lead time *(assumed)*); table of recent POs (number, date, status, received %).
- **Key interactions**: date-range filter *(assumed)*; click PO → PO view; no writes.
- **States**: empty (no POs yet → "no orders for this supplier") · loading (stat skeleton) · error (retry) · selected (PO row → PO).
- **Gating**: `operations.suppliers.view-any`.

## Data

- Owns / writes: nothing.
- Reads: `ops_purchase_orders`, `ops_goods_receipts`/`ops_grn_lines` (same domain, read-only).
- Cross-domain writes: none.

## Relations

- Consumes: reads PO + GRN rows (same-domain read, not events).
- Feeds: metrics reused by [[../../operations-reporting/_module|operations.reporting]] supplier section.
- Shared entity: PO/GRN tables owned by their modules; this feature only reads them.

## Related

- [[../_module|Suppliers]] · [[../../purchase-orders/_module|Purchase Orders]] · [[../../goods-receipt/_module|Goods Receipt]]
