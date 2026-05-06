---
tags: [flowflex, domain/operations, inventory, stock, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-06
---

# Inventory Management

Real-time stock control across multiple warehouses. Reorder automation and full traceability.

**Who uses it:** Operations managers, warehouse staff, purchasing
**Filament Panel:** `operations`
**Depends on:** Core
**Phase:** 4
**Build complexity:** Very High — 3 resources, 2 pages, 8 tables

## Events Fired

- `StockBelowReorderPoint` → consumed by [[Purchasing & Procurement]] (create draft PO)

## Events Consumed

- `OrderPlaced` (from E-commerce) → deducts stock from inventory
- `FieldJobCompleted` (from [[Field Service Management]]) → deducts parts used on-site

## Features

- **Real-time stock levels** — per product per location
- **Multi-location warehouse support** — track stock across multiple sites
- **Reorder point alerts** — auto-create draft PO when stock falls below threshold
- **Barcode / QR scanning** — mobile app for receiving and picking
- **Batch and serial number management** — full traceability
- **Stock adjustments** — write-off, damage correction, shrinkage
- **Supplier tracking** — preferred supplier per product
- **Stock forecasting** — demand-based reorder quantity suggestions
- **Costing methods** — FIFO / LIFO / FEFO
- **Returns and RMA handling** — customer returns back into stock
- **Stocktake / cycle count workflows** — guided counting process

## Database Tables (8)

1. `products` — product master
2. `stock_locations` — warehouse/location definitions
3. `stock_levels` — current quantity per product per location
4. `stock_movements` — every stock movement audit trail
5. `stock_adjustments` — manual adjustment records
6. `batch_serials` — batch/serial tracking records
7. `reorder_rules` — reorder point and quantity per product
8. `cycle_counts` — stocktake sessions and results

## Related

- [[Operations Overview]]
- [[Purchasing & Procurement]]
- [[Asset Management]]
- [[Field Service Management]]
- [[Point of Sale]]
- [[Order Management]]
- [[Fixed Asset & Depreciation]]
