---
domain: operations
module: stock-adjustments
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Stock Adjustments — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers)

- **€500 threshold** — the approval threshold value is *(assumed)*; it is a company setting. Confirm default + whether it's per-adjustment value or cumulative.
- **`status` enum** — `pending-approval` / `applied` simple flag is *(assumed)* (vs a state machine).
- **`notes` required for theft/write-off** — *(assumed)*. Confirm which reason codes mandate a note.
- **Reason code set** — the six codes (damage, loss, theft, stocktake, write-off, found) are *(assumed)* the full v1 set.
- **GL posting deferred** — the write-off-report-instead-of-event decision is *(assumed)* (see [[./decisions]]).

## Open Questions

- **Stocktake freeze** — during a stocktake count, should stock movements be frozen for the warehouse to avoid count drift? v1 does not freeze *(assumed)*; deltas are computed at confirm time against then-current levels. Confirm acceptable.
- **Cycle counting** — recurring partial stocktakes (count a slice of SKUs on a schedule) vs full-warehouse count. v1 is full-count on demand *(assumed)*; cycle-count scheduling ties to the mobile/barcode gap in [[../../_opportunities]].
