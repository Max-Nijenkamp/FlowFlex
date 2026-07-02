---
domain: operations
module: goods-receipt
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Goods Receipt — Decisions & ADR Notes

## GRN Is the Single Finance Trigger

**Context:** Finance AP needs to know when to draft a supplier bill and run a 3-way match.

**Decision:** The GRN — the actual physical receipt — is the sole event source. `GoodsReceived` fires with **accepted totals only** (rejected quantities never bill). Neither PO send nor stock movement fires a finance event.

**Consequences:** A clean, auditable 3-way match (PO ordered ↔ GRN accepted ↔ bill). Rejected goods never create a payable. If finance.ap is inactive the event fires harmlessly unconsumed.

---

## Atomic Receipt Across Three Concerns

**Decision:** `GrnService::receive` writes the GRN, posts accepted stock (`StockService::move`), updates the PO (`recordReceipt`), and fires `GoodsReceived` — all in **one transaction**. Either everything commits or nothing does.

**Consequences:** No half-received state (stock in but PO not updated, or event fired without stock). Stock/PO updates are synchronous same-domain calls; only the finance effect is async (event → queued listener).

---

## Over-Receipt Tolerance

**Decision:** Cumulative received per line may exceed ordered by up to 10% *(assumed)*; beyond that the receipt is rejected. Rejected quantity always requires a reason.

**Consequences:** Small over-deliveries (common in bulk goods) are accepted without a PO amendment; gross over-receipts are blocked. The 10% figure is assumed — see [[./unknowns]].
