---
domain: operations
module: stock-adjustments
feature: adjustment-approval
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Adjustment & Approval

Record a single stock correction; high-value ones wait for a second signer before touching stock.

## Behaviour

- `AdjustmentService::adjust`: computes `value_impact_cents` (delta × item cost). Under threshold → `applied` + `StockService::move(adjust)`. Over threshold → `pending-approval` (stock untouched).
- `AdjustmentService::approve`: approver ≠ adjuster; flips to `applied` + posts the movement.
- Negative delta beyond available rejected. `notes` required for theft/write-off.

## UI

- **Kind**: simple-resource — table + form + approve action + pending tab.
- **Page**: `StockAdjustmentResource` at `/operations/adjustments`.
- **Layout**: tabs (All / Pending); table (item, warehouse, delta, reason, value impact, status, adjuster/approver); form (item, warehouse, delta, reason, notes); approve action on pending rows.
- **Key interactions**: create adjustment (applies or queues by threshold); approve pending (blocked for the adjuster); filter by reason/period.
- **States**: empty (no adjustments → CTA) · loading (skeleton) · error (negative beyond available; self-approval blocked; missing note) · selected (pending row → approve modal).
- **Gating**: view `operations.adjustments.view-any`; create `.create`; approve `.approve`.

## Data

- Owns / writes: `ops_stock_adjustments`.
- Reads: `ops_items` (cost for value impact), `ops_stock_levels` (availability), warehouse names.
- Cross-domain writes: none — the delta is applied via `StockService::move(adjust)`; no GL write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: applied movements into inventory's ledger; value impacts into the write-off report.
- Shared entity: `ops_items`, `ops_warehouses`.

## Related

- [[../_module|Stock Adjustments]] · [[./stocktake|Stocktake]] · [[./write-off-report|Write-Off Report]]
