---
domain: operations
module: inventory
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Inventory — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers)

- **FIFO deferred** — valuation is weighted-average; FIFO/lot-cost is *(assumed)* deferred to an ADR (see [[./decisions]]).
- **Delete guard** — item soft-delete blocked while stock > 0 is *(assumed)*. Confirm exact behaviour.
- **`unit_cost_cents` required on `in`** — *(assumed)* receipts must carry cost. Confirm whether a costless receipt is ever valid (e.g. free samples → cost 0).
- **Meilisearch fields** — item index (`sku`, `name`, `category`) is *(assumed)*.
- **No realtime** — level changes are not broadcast in v1 *(assumed)*; the movement ledger is read on page load.
- **Barcode lookup** — SKU/barcode scan lookup mentioned in the flat spec but no dedicated barcode column; *(assumed)* SKU doubles as barcode for v1. See [[../../_opportunities]] (mobile barcode gap).

## Open Questions

- **Lot / batch / serial + expiry tracking** — not in v1 tables. Real demand exists (food, pharma, regulated goods — [[../../_opportunities]]). Confirm whether a `lot_number` / `expiry_date` dimension on movements is a v1.x fast-follow or a separate module.
- **Negative stock** — is a hard block correct, or should certain flows allow oversell with backorder? Currently hard block via `InsufficientStockException`. *(assumed)*
