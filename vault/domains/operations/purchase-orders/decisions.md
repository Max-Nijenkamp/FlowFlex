---
domain: operations
module: purchase-orders
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Purchase Orders — Decisions & ADR Notes

## PO Fires No Events — GRN Does

**Context:** The 3-way match / draft-bill flow needs a signal to Finance when goods arrive.

**Decision:** The PO module fires **no** cross-domain events. Receiving is modelled entirely in [[../goods-receipt/_module|goods-receipt]], which fires `GoodsReceived`. The GRN calls back into `PurchaseOrderService::recordReceipt` (same domain) to update line receipts + status.

**Consequences:** One clear event source for Finance (the actual receipt), not the PO. The PO stays a pure purchasing document. Cost: PO status changes are driven by GRN, so a PO built without the GRN module tracks status manually (soft dep).

---

## Receiving Is a Same-Domain Call, Not an Event

**Decision:** GRN → PO status update is a direct `recordReceipt` service call within Operations, not an event. Stock updates likewise stay in-domain (`StockService::move`). Only the Finance-facing effect (`GoodsReceived`) crosses a domain boundary as an event.

**Consequences:** Tight, synchronous, transactional receipt handling inside Operations; async only where a real bounded-context boundary is crossed (Finance).

---

## Line Cost Defaults From Preferred Supplier

**Decision:** A PO line's `unit_cost_cents` defaults from the preferred `ops_supplier_items` cost for that item+supplier. If neither a catalogue cost nor a manual cost is given, creation fails with "No cost known for this item from this supplier."

**Consequences:** Deterministic totals; forces an explicit cost decision rather than silently defaulting to 0.
