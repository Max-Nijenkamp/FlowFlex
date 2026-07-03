---
domain: operations
module: operations-reporting
feature: dead-stock-report
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Dead-Stock & Turnover

Flag items with no movement in N days and compute stock-turnover ratio per item.

## Behaviour

- Dead stock: items with no `ops_stock_movements` in the last N days (default 90 *(assumed)*) but on-hand > 0.
- Turnover ratio per item over the period *(assumed COGS / average inventory)*.
- A scheduled background job may precompute the dead-stock list into the metrics cache; the widget also reads on demand.

## UI

- **Kind**: background (precompute job) + widget (`DeadStockWidget`). No standalone page.
- **Page**: `DeadStockWidget` on `OperationsDashboardPage`; underlying scheduled precompute task.
- **Layout**: table of dead items (SKU, name, on-hand, value tied up, days since last movement); turnover column; Excel export.
- **Key interactions**: adjust the N-day window; sort by value tied up; export; link to item / start a write-off.
- **States**: empty (nothing stale → "no dead stock") · loading (skeleton) · error (retry) · selected (row → item).
- **Gating**: `operations.reporting.view`.

## Data

- Owns / writes: nothing (reads + cache).
- Reads: `ops_stock_movements`, `ops_stock_levels`, `ops_items`.
- Cross-domain writes: none. Acting on dead stock (write-off) hands off to [[../../stock-adjustments/_module|operations.adjustments]], which writes its own table.

## Relations

- Consumes: nothing.
- Feeds: dead-stock list can seed a write-off adjustment (user-initiated).
- Shared entity: inventory tables (read-only).

## Test Checklist

### Unit
- [ ] Dead-stock rule: no movements in N days (default 90 *(assumed)*) AND on-hand > 0; turnover ratio math

### Feature (Pest)
- [ ] Item with a movement inside the window excluded; zero on-hand excluded
- [ ] Tenant isolation: lists scoped to own-company stock

### Livewire
- [ ] `DeadStockWidget` renders list; hidden without `operations.reporting.view`

## Related

- [[../_module|Operations Reporting]] · [[../../stock-adjustments/_module|Stock Adjustments]] · [[../../../foundation/queue-workers/_module|foundation.queues]]
