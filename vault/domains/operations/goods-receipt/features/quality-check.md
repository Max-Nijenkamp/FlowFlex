---
domain: operations
module: goods-receipt
feature: quality-check
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Quality Check (Accept / Reject)

Accept or reject received quantities per line, with a reason on rejection. Only accepted stock enters inventory.

## Behaviour

- Per GRN line: `quantity_accepted` posts a stock `in` movement; `quantity_rejected` posts nothing.
- `reject_reason` required whenever `quantity_rejected > 0`.
- Rejected goods never enter stock and never bill (accepted totals only in `GoodsReceived`).
- Discrepancy (received ≠ ordered) flagged for visibility.

## UI

- **Kind**: custom-page — part of the `ReceiveGoodsPage` grid (accept/reject columns + reason), not a separate screen.
- **Page**: accept/reject columns within `ReceiveGoodsPage` at `/operations/goods-receipts/receive`.
- **Layout**: per line, accepted + rejected inputs with a reason field that appears when rejected > 0; row highlights on discrepancy.
- **Key interactions**: adjust accepted/rejected split; reason enforced; totals + discrepancy recompute live.
- **States**: error (reject without reason; split ≠ received) · selected (line flagged discrepant) · empty/loading inherited from the receiving page.
- **Gating**: `operations.goods-receipt.create`.

## Data

- Owns / writes: `ops_grn_lines` (`quantity_accepted`, `quantity_rejected`, `reject_reason`).
- Reads: PO line ordered qty for discrepancy comparison.
- Cross-domain writes: none — accepted qty reaches stock via `StockService::move` only.

## Relations

- Consumes: nothing.
- Feeds: accepted totals into `GoodsReceived` (rejected excluded).
- Shared entity: `ops_po_lines` (operations.purchase-orders).

## Test Checklist

### Unit
- [ ] `quantity_accepted + quantity_rejected = quantity_received` enforced per line
- [ ] `reject_reason` required whenever `quantity_rejected > 0`

### Feature (Pest)
- [ ] Only `quantity_accepted` posts a stock `in` movement; `quantity_rejected` posts nothing
- [ ] `GoodsReceived` payload carries accepted totals only (rejected excluded)
- [ ] Discrepancy (received ≠ ordered) is flagged but does not block acceptance

### Livewire
- [ ] Reason field appears when rejected > 0; submit blocked without it
- [ ] Totals + discrepancy flags recompute live as the accept/reject split changes

## Related

- [[../_module|Goods Receipt]] · [[./receiving|Receiving]]
