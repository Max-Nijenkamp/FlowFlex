---
domain: operations
module: inventory
feature: valuation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Valuation

Weighted-average stock valuation, per warehouse or company-wide.

## Behaviour

- `StockService::valuation(?warehouseId): Money` = Σ `on_hand × cost_price_cents`, brick/money (no float).
- `cost_price_cents` is the weighted average, recomputed on every `in` movement: `(on_hand·old + qty_in·unit_cost_in) / (on_hand + qty_in)`.
- Reserved stock still counts toward valuation (on-hand basis) until shipped.
- FIFO deferred *(assumed)*.

## UI

- **Kind**: widget — a valuation figure/breakdown surfaced on the item view and the Operations dashboard; deeper reporting lives in [[../../operations-reporting/_module|operations.reporting]].
- **Page**: `ValuationWidget` (Filament widget) — total value + by-warehouse/category breakdown.
- **Layout**: stat card(s): total stock value; small table by warehouse; optional by-category.
- **Key interactions**: warehouse/category filter (mirrors reporting dashboard); no writes.
- **States**: empty (no stock → €0.00) · loading (stat skeleton) · error (retry) · selected (n/a).
- **Gating**: `operations.inventory.view-any` (reporting dashboard version gates on `operations.reporting.view`).

## Data

- Owns / writes: nothing (read/compute over `ops_stock_levels` + `ops_items`).
- Reads: own module tables only.
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: valuation numbers reused by [[../../operations-reporting/_module|operations.reporting]] via `StockService::valuation`.
- Shared entity: none.

## Related

- [[../_module|Inventory]] · [[../../operations-reporting/_module|operations.reporting]]
