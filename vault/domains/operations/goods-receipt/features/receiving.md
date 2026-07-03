---
domain: operations
module: goods-receipt
feature: receiving
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Receiving

Receive goods against a PO — prefilled from open quantities, accept/reject per line, post stock atomically.

## Behaviour

- Start from a `sent`/`partially_received` PO; lines prefill with open (ordered − received) quantities.
- Per line, enter received / accepted / rejected (`accepted + rejected = received`); rejected needs a reason.
- On submit, `GrnService::receive` runs one transaction: GRN rows → accepted stock `in` at PO cost → `recordReceipt` (PO status) → fire `GoodsReceived`.
- Partial receipt leaves the PO `partially_received`; completing all lines → `received`.
- Over-receipt beyond tolerance rejected before any write.

## UI

- **Kind**: custom-page — a receiving screen with a per-line accept/reject grid and running discrepancy totals, beyond a plain form ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `ReceiveGoodsPage` at `/operations/goods-receipts/receive` (and read-only `GoodsReceiptResource` for history).
- **Layout**: PO header (supplier, expected date, receiving warehouse); line grid (item, ordered, already received, receiving now, accepted, rejected, reason); footer totals + discrepancy flags; confirm button.
- **Key interactions**: pick PO → grid prefills open qty; edit accepted/rejected (validation live); reason required on reject; confirm → atomic post → GRN created, stock updated, event fired.
- **States**: empty (no open POs → "nothing to receive") · loading (post spinner) · error (accepted+rejected≠received, over-receipt, missing reason → inline, no write) · selected (PO chosen, grid active).
- **Gating**: view `operations.goods-receipt.view-any`; receive `operations.goods-receipt.create`.

## Data

- Owns / writes: `ops_goods_receipts`, `ops_grn_lines`.
- Reads: PO + lines (operations.purchase-orders), warehouse (operations.warehouses).
- Cross-domain writes: none — stock via `StockService::move`, PO via `recordReceipt` (same-domain); the Finance bill is finance.ap's own listener reacting to `GoodsReceived` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `GoodsReceived` → finance.ap (draft bill + 3-way match).
- Shared entity: `ops_purchase_orders`, `ops_items`, `ops_warehouses`.

## Test Checklist

### Unit
- [ ] Line prefill computes open qty = ordered − already-received
- [ ] Over-receipt guard rejects cumulative received > ordered × 1.1 *(assumed 10%)*

### Feature (Pest)
- [ ] `GrnService::receive` posts GRN + accepted stock `in` at PO cost + PO status update + `GoodsReceived` in one transaction; a failure in any step rolls back all
- [ ] Partial receipt leaves PO `partially_received`; receiving remaining lines flips it to `received`
- [ ] Tenant isolation: cannot receive against another company's PO (denied before any write)
- [ ] Concurrent double-receive of the same PO line serialised via `lockForUpdate` — no double stock post

### Livewire
- [ ] Picking a PO prefills the line grid with open quantities
- [ ] Submit denied without `operations.goods-receipt.create`; validation error (split ≠ received) blocks the write

## Related

- [[../_module|Goods Receipt]] · [[./quality-check|Quality Check]] · [[./three-way-match-event|GoodsReceived Event]]
