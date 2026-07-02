---
domain: operations
module: inventory
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Inventory

Stock item catalogue, quantity tracking per warehouse, reorder points, and weighted-average valuation. The core of Operations — every other module reads or moves stock through it.

> Operations hosts the [[../../procurement/_index|Procurement]] panel. See [[../../../decisions/decision-2026-06-01-panel-consolidation]].

---

## Module-key

`operations.inventory`

**Priority:** p3
**Panel:** operations (Orange)
**Permission prefix:** `operations.inventory`
**Tables:** `ops_items`, `ops_stock_levels`, `ops_stock_movements`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../warehouses/_module\|operations.warehouses]] | stock levels are per-warehouse |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Soft | [[../../core/data-import/_module\|core.import]] | bulk item import |
| Soft | [[../purchase-orders/_module\|operations.purchase-orders]] / [[../stock-adjustments/_module\|operations.adjustments]] | movement sources |

---

## Core Features

- Inventory item: SKU, name, category, unit of measure, cost price, reorder point
- Stock levels per warehouse (on-hand, reserved, available = on-hand − reserved)
- **`StockService::move()` = the single write path** — every change (receipt, transfer, adjustment, sale) is a movement row; levels are derived, never edited directly
- Weighted-average cost valuation *(assumed: FIFO deferred)*
- Reserve / release for sales & e-commerce hooks
- Low-stock detection (available < reorder point)
- Item import via Core Data Import

See features: [[./features/item-catalogue|Item Catalogue]] · [[./features/stock-movements|Stock Movements Ledger]] · [[./features/reserve-release|Reserve & Release]] · [[./features/valuation|Valuation]] · [[./features/low-stock-alerts|Low-Stock Alerts]].

---

## Build Manifest

```
database/migrations/xxxx_create_ops_items_table.php
database/migrations/xxxx_create_ops_stock_levels_table.php
database/migrations/xxxx_create_ops_stock_movements_table.php
app/Models/Operations/{Item,StockLevel,StockMovement}.php
app/Data/Operations/{CreateItemData,MoveStockData}.php
app/Contracts/Operations/StockServiceInterface.php
app/Services/Operations/StockService.php
app/Providers/Operations/OperationsServiceProvider.php
app/Exceptions/Operations/InsufficientStockException.php
app/Filament/Operations/Resources/{ItemResource,StockMovementResource}.php
app/Filament/Operations/Widgets/LowStockWidget.php
database/factories/Operations/{ItemFactory,StockLevelFactory}.php
tests/Feature/Operations/{StockMovementTest,ValuationTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] All level changes produce movement rows; direct level edit impossible (no write path)
- [ ] Out beyond available rejected (`InsufficientStockException`)
- [ ] Weighted-average cost update on receipts (fixture math)
- [ ] Reserve/release affects available, not on-hand
- [ ] Valuation = Σ qty × cost (brick/money)
- [ ] Low-stock uses available vs reorder point

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Provides | `StockService` (read + `move`) | warehouses, PO, GRN, adjustments, ecommerce, sales | the only stock write path in the app |

**Data ownership:** `operations.inventory` writes only `ops_items`, `ops_stock_levels`, `ops_stock_movements`. Other modules mutate stock exclusively by calling `StockService::move` (same-domain) — no other service writes these tables ([[../../../security/data-ownership]]). Inventory fires **no** cross-domain events; the `GoodsReceived` finance event is fired by [[../goods-receipt/_module|goods-receipt]], not here.

---

## Related

- [[../warehouses/_module|operations.warehouses]]
- [[../stock-adjustments/_module|operations.adjustments]]
- [[../purchase-orders/_module|operations.purchase-orders]]
- [[../_index|Operations MOC]]
