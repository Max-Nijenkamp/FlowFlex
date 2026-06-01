---
type: module
domain: Operations
panel: operations
module-key: operations.inventory
status: planned
color: "#4ADE80"
---

# Inventory

Stock item records, quantity tracking per warehouse, reorder points, and stock valuation. The core of the Operations domain.

## Core Features

- Inventory item: SKU, name, description, category, unit of measure, cost price, reorder point
- Stock levels per warehouse (quantity on hand, reserved, available)
- Reorder alerts when stock falls below reorder point
- Stock valuation: quantity × cost (FIFO or weighted average)
- Barcode/SKU lookup
- Stock movements log (in/out/transfer/adjustment)
- Low-stock dashboard
- Import items via Core Data Import

## Data Model

| Table | Key Columns |
|---|---|
| `ops_items` | company_id, sku, name, category, unit, cost_price_cents, reorder_point |
| `ops_stock_levels` | company_id, item_id, warehouse_id, quantity_on_hand, quantity_reserved |
| `ops_stock_movements` | company_id, item_id, warehouse_id, type (in/out/transfer/adjust), quantity, reference, occurred_at |

## Filament

**Nav group:** Inventory

- `ItemResource` — list, create, edit; stock levels per warehouse shown inline
- `StockMovementResource` — movement history
- `LowStockWidget` — items below reorder point

## Related

- [[domains/operations/warehouses]]
- [[domains/operations/stock-adjustments]]
- [[domains/operations/purchase-orders]]
