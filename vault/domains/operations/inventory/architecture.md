---
domain: operations
module: inventory
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Inventory — Architecture

## The single write path

`ops_stock_levels` is **never** written outside `StockService::move`. Every quantity change in the whole app (receipts, transfers, adjustments, sales, e-commerce) routes through it. Levels are a derived projection of the append-only `ops_stock_movements` ledger.

## Services & Actions

Interface→Service: `StockServiceInterface` → `StockService` (bound in `OperationsServiceProvider`).

| Method | Notes |
|---|---|
| `move(MoveStockData $data): void` | one transaction: writes a movement row + upserts the level. On `in`, recomputes weighted-average `cost_price_cents`. Throws `InsufficientStockException` when an `out`/`transfer-out` exceeds available. |
| `reserve(itemId, warehouseId, qty): void` | raises `quantity_reserved` (available drops; on-hand unchanged). Sales/e-commerce hook. |
| `release(itemId, warehouseId, qty): void` | lowers `quantity_reserved`. |
| `valuation(?string $warehouseId = null): Money` | Σ `on_hand × cost_price_cents`, brick/money. |
| `lowStock(): Collection` | items where any warehouse available < `reorder_point`. |

**Weighted average on receipt:** `new_cost = (on_hand·old_cost + qty_in·unit_cost_in) / (on_hand + qty_in)`, computed in integer cents via brick/money (no float). *(assumed FIFO deferred — ADR if needed.)*

---

## Events

Fires none. Consumes none. Cross-module stock effects are same-domain method calls into `StockService`, not events. The Finance-facing `GoodsReceived` event belongs to [[../goods-receipt/_module|goods-receipt]].

---

## Filament Artifacts

**Nav group:** Inventory

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ItemResource` | #1 CRUD resource | per-warehouse levels inline (relation), SKU search |
| `StockMovementResource` | #1 (read-only) | ledger history, filters by item/warehouse/type/date |
| `LowStockWidget` | #6 widget | items below reorder point |

**Access contract:** `canAccess() = Auth::user()->can('operations.inventory.view-any') && BillingService::hasModule('operations.inventory')` per [[../../../architecture/filament-patterns]] #1.

---

## Search & Realtime

- Meilisearch (Scout): items indexed on `sku`, `name`, `category` for global + panel search *(assumed)*.
- No realtime on level changes in v1 — the movement ledger is read on load *(assumed)*.
