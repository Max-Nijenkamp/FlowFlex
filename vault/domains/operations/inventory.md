---
type: module
domain: Operations
domain-key: operations
panel: operations
module-key: operations.inventory
status: planned
priority: p3
depends-on: [operations.warehouses, core.billing, core.rbac]
soft-depends: [core.import, operations.purchase-orders, operations.adjustments]
fires-events: []
consumes-events: []
patterns: [service, money]
tables: [ops_items, ops_stock_levels, ops_stock_movements]
permission-prefix: operations.inventory
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Inventory

Stock item records, quantity tracking per warehouse, reorder points, and stock valuation. The core of the Operations domain.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/warehouses\|operations.warehouses]] | stock levels per warehouse (warehouses build first — tiny module) |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/core/data-import\|core.import]] | item import |
| Soft | purchase-orders / adjustments | movement sources |

---

## Core Features

- Inventory item: SKU, name, description, category, unit of measure, cost price, reorder point
- Stock levels per warehouse (quantity on hand, reserved, available = on hand − reserved)
- Reorder alerts when available stock falls below reorder point
- Stock valuation: weighted average cost *(assumed: FIFO deferred — ADR if needed)*
- Barcode/SKU lookup
- **Stock movements log = single write path**: every change (receipt, transfer, adjustment, sale) goes through `StockService::move()` — levels are derived, never edited directly
- Low-stock dashboard
- Import items via Core Data Import

---

## Data Model

### ops_items

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| sku | string | unique `(company_id, sku)` |
| name | string | |
| category | string nullable | |
| unit | string | piece / kg / m … |
| cost_price_cents | bigint | weighted average, updated on receipt |
| reorder_point | decimal(12,2) | 0 = no alert |
| deleted_at | timestamp nullable | blocked while stock > 0 *(assumed)* |

### ops_stock_levels — id, company_id (indexed), item_id FK, warehouse_id FK, quantity_on_hand decimal(12,2), quantity_reserved decimal(12,2); unique `(item_id, warehouse_id)`

### ops_stock_movements

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), item_id FK, warehouse_id FK | ulid | |
| type | string | in / out / transfer-in / transfer-out / adjust |
| quantity | decimal(12,2) | signed by type semantics |
| unit_cost_cents | bigint nullable | receipts carry cost |
| reference_type / reference_id | string / ulid nullable | GRN, transfer, adjustment |
| occurred_at | timestamp | |

**Indexes:** `(company_id, item_id, occurred_at)` — append-only.

---

## DTOs

### CreateItemData — sku (unique per company), name, unit, cost_price_cents (min:0), reorder_point, category?
### MoveStockData — item_id, warehouse_id, type (in set), quantity (> 0), unit_cost_cents?, reference

## Services & Actions

Interface→Service: `StockServiceInterface` → `StockService`.

- `move(MoveStockData $data): void` — transaction: movement row + level upsert + weighted-avg cost update on `in`; throws `InsufficientStockException` on out > available
- `reserve(itemId, warehouseId, qty)` / `release(...)` — e-commerce/sales hooks
- `valuation(?string $warehouseId): Money`
- `lowStock(): Collection`

---

## Filament

**Nav group:** Inventory

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ItemResource` | #1 CRUD resource | per-warehouse levels inline; SKU search |
| `StockMovementResource` | #1 (read-only) | history, filters |
| `LowStockWidget` | #6 widget | below reorder point |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('operations.inventory.view-any') && BillingService::hasModule('operations.inventory')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`operations.inventory.view-any` · `operations.inventory.manage-items` · `operations.inventory.move-stock`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] All level changes produce movement rows; direct level edit impossible (no write path)
- [ ] Out beyond available rejected
- [ ] Weighted-average cost update on receipts (fixture math)
- [ ] Reserve/release affects available, not on-hand
- [ ] Valuation = Σ qty × cost (brick/money)
- [ ] Low-stock uses available vs reorder point

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

## Related

- [[domains/operations/warehouses]]
- [[domains/operations/stock-adjustments]]
- [[domains/operations/purchase-orders]]
