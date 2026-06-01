---
type: module
domain: Operations
panel: operations
module-key: operations.reporting
status: planned
color: "#4ADE80"
---

# Operations Reporting

Inventory valuation, stock movement trends, supplier performance, and purchasing spend dashboards.

## Core Features

- Inventory valuation report: total stock value by warehouse/category
- Stock movement trends: in/out over time
- Low-stock and out-of-stock report
- Supplier performance: on-time delivery, order accuracy
- Purchasing spend: by supplier, by category, over time
- Stock turnover ratio per item
- Dead stock report (no movement in N days)
- Export to Excel

## Data Model

No additional tables. Aggregates from `ops_items`, `ops_stock_levels`, `ops_stock_movements`, `ops_purchase_orders`, `ops_suppliers`.

## Filament

**Nav group:** Reporting

- `OperationsDashboardPage` (custom dashboard) — chart widgets
- Export via `pxlrbt/filament-excel`

## Related

- [[domains/operations/inventory]]
- [[domains/operations/suppliers]]
- [[architecture/performance]]
