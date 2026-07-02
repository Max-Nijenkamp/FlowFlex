---
domain: operations
module: purchase-orders
feature: po-lifecycle
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: PO Lifecycle

Create a PO, send it, and track receipt through the status machine.

## Behaviour

State machine on `ops_purchase_orders.status`:

- `draft → sent` (`send` permission): assign `po_number`, queue PDF + supplier mail.
- `sent → partially_received`: GRN accepts some lines (via `recordReceipt`).
- `sent`/`partially_received → received`: GRN completes all lines.
- `draft`/`sent → cancelled`: blocked once any receipt exists.

Totals via brick/money; line cost defaults from the preferred supplier catalogue. Transitions audited.

## UI

- **Kind**: simple-resource — table + form + repeater + modal actions (send/cancel). No board needed.
- **Page**: `PurchaseOrderResource` at `/operations/purchase-orders`.
- **Layout**: table (PO number, supplier, status badge, total, expected delivery, receipt %); form with line repeater (item, qty, unit cost auto-defaulted); view page shows status timeline + receipt progress + PDF preview.
- **Key interactions**: add lines (cost auto-fills from catalogue); `send` action (confirm → PDF/mail queued); `cancel` action (blocked after receipt); receipt progress columns update as GRNs land.
- **States**: empty (no POs → "create your first PO" CTA) · loading (table skeleton) · error (missing line cost → "No cost known…"; cancel-after-receipt rejected) · selected (row → view with timeline).
- **Gating**: view `operations.purchase-orders.view-any`; create `.create`; send `.send`; cancel `.cancel`.

## Data

- Owns / writes: `ops_purchase_orders`, `ops_po_lines`.
- Reads: `ops_suppliers` + `ops_supplier_items` (cost default), `ops_items`.
- Cross-domain writes: none — GRN updates receipts via `recordReceipt` (same-domain service), not a direct table write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `recordReceipt` calls from operations.goods-receipt (same domain).
- Feeds: nothing (no event; the receipt event is GRN's `GoodsReceived`).
- Shared entity: `ops_suppliers`, `ops_items`.

## Related

- [[../_module|Purchase Orders]] · [[./pdf-and-email|PDF & Email]] · [[../../goods-receipt/_module|Goods Receipt]]
