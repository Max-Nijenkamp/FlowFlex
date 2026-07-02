---
domain: operations
module: inventory
feature: low-stock-alerts
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Low-Stock Alerts

Detect and surface items whose available stock has fallen below their reorder point.

## Behaviour

- `StockService::lowStock()` returns items where any warehouse `available < reorder_point` (`reorder_point = 0` = no alert).
- A scheduled background job evaluates low stock (e.g. hourly *(assumed)*) and may notify / feed a reorder suggestion; the live widget also computes on demand.
- Reorder-point per item; multi-warehouse aggregation *(assumed: alert if any single warehouse below point)*.

## UI

- **Kind**: background (detection job) + widget (surface). No standalone page.
- **Page**: `LowStockWidget` on the Operations dashboard; underlying `EvaluateLowStockJob` scheduled task.
- **Layout**: widget = list/table of low items (SKU, name, available, reorder point, suggested reorder qty *(assumed)*); links to create a PO.
- **Key interactions**: click item → item view / start a purchase order (soft dep on PO module); no writes from the widget itself.
- **States**: empty (all above reorder → "stock healthy") · loading (skeleton rows) · error (retry) · selected (row → item).
- **Gating**: `operations.inventory.view-any`.

## Data

- Owns / writes: nothing — pure read over `ops_items` + `ops_stock_levels`.
- Reads: own module tables.
- Cross-domain writes: none. Creating a PO from a low-stock row hands off to [[../../purchase-orders/_module|operations.purchase-orders]], which writes its own tables.

## Relations

- Consumes: nothing.
- Feeds: low-stock list feeds the Operations dashboard + can seed a PO (user-initiated).
- Shared entity: none.

## Related

- [[../_module|Inventory]] · [[../../purchase-orders/_module|operations.purchase-orders]] · [[../../../foundation/queue-workers/_module|foundation.queues]]
