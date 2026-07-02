---
domain: operations
module: purchase-orders
feature: pdf-and-email
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: PO PDF & Supplier Email

Generate a PO PDF and email it to the supplier on send.

## Behaviour

- On `draft → sent`, `GeneratePoPdfJob` renders the PO (spatie/laravel-pdf) → stores `pdf_path`.
- `PurchaseOrderMail` (queued, `ShouldQueue`, extends `FlowFlexMailable`) emails the PDF to the supplier.
- Both are **rate-limited per company** to prevent PDF/email abuse ([[../security]]).
- PDF is previewable on the PO view page.

## UI

- **Kind**: background — the PDF render + email are queued jobs; the only UI is the "Send" action + a PDF preview link on `PurchaseOrderResource`.
- **Trigger**: `send` action dispatches `GeneratePoPdfJob` then `PurchaseOrderMail`; preview link opens the stored `pdf_path`.
- **States** (of the send action): loading (queued toast) · error (throttled → "please wait" / job failure surfaced) · done (PDF preview available).
- **Gating**: `operations.purchase-orders.send`.

## Data

- Owns / writes: `ops_purchase_orders.pdf_path`.
- Reads: PO + lines + supplier (own + suppliers module) to render.
- Cross-domain writes: none. Outbound email is a side-effect, not a data write.

## Relations

- Consumes: nothing.
- Feeds: nothing (email is outbound to the supplier, not a domain event).
- Shared entity: `ops_suppliers` (recipient email).

## Related

- [[../_module|Purchase Orders]] · [[./po-lifecycle|PO Lifecycle]] · [[../../../foundation/queue-workers/_module|foundation.queues]] · [[../../../../architecture/email]]
