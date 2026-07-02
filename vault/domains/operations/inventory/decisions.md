---
domain: operations
module: inventory
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Inventory — Decisions & ADR Notes

## Levels Derived From an Append-Only Ledger

**Context:** Stock quantities could be a simple editable number per item/warehouse.

**Decision:** `ops_stock_levels` is a derived projection; the source of truth is the append-only `ops_stock_movements` ledger. Every change is a movement written by `StockService::move`; the level row is upserted in the same transaction. Direct level edits are impossible (no write path, no resource form field).

**Consequences:** Full auditability (every unit traced to a receipt/transfer/adjustment), and the single-write-path invariant that lets the data-ownership arch test hold. Cost: a `move` is heavier than a counter update.

---

## Weighted-Average Cost (FIFO Deferred)

**Context:** Valuation needs a cost basis.

**Decision:** v1 uses weighted-average cost, recomputed on each `in` movement in integer cents via brick/money. FIFO / lot-cost layers are deferred.

**Consequences:** Simpler math, one `cost_price_cents` per item. FIFO would need cost layers per receipt — a future ADR (and links to lot/batch tracking, see [[../../_opportunities]]).

---

## Reserve/Release Affects Available, Not On-Hand

**Context:** Sales orders and e-commerce carts need to hold stock without removing it.

**Decision:** `reserve` raises `quantity_reserved`; `available = on_hand − reserved`. On-hand only changes on an actual `out` movement (fulfilment). Release lowers reserved.

**Consequences:** E-commerce/sales can hold stock safely; valuation still counts reserved on-hand until shipped.
