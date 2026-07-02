---
domain: operations
module: inventory
feature: stock-movements
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Stock Movements Ledger & Stock Board

The append-only movement ledger plus a per-warehouse stock board — the single view of what is where.

## Behaviour

- Every stock change is a `ops_stock_movements` row written by `StockService::move` (receipt, transfer, adjustment, sale).
- Ledger is read-only and append-only — no edit/delete.
- A **stock board** aggregates current levels by item × warehouse (on-hand / reserved / available), with a manual `move` action for authorised users.
- Manual `out`/`transfer-out` beyond available is rejected (`InsufficientStockException`).

## UI

- **Kind**: custom-page — the multi-warehouse stock board is a matrix/board view beyond simple table+form ([[../../../../architecture/patterns/custom-pages]]). The raw ledger is a read-only simple-resource (`StockMovementResource`).
- **Page**: `StockBoardPage` at `/operations/stock-board`; `StockMovementResource` at `/operations/stock-movements`.
- **Layout**: board = items as rows, warehouses as columns, cell shows available (on-hand/reserved tooltip); header totals per warehouse; row action "record movement". Ledger = filterable table (item, warehouse, type, date, reference).
- **Key interactions**: filter/search the ledger; on the board, click a cell → `move` modal (type, qty, cost, reason) → optimistic level update; over-available move → error toast, no write.
- **States**: empty (no items → CTA to catalogue) · loading (board skeleton grid) · error (insufficient-stock toast; card reverts) · selected (cell highlighted, move modal open).
- **Gating**: view `operations.inventory.view-any`; manual move `operations.inventory.move-stock`.

## Data

- Owns / writes: `ops_stock_movements`, `ops_stock_levels` (both via `StockService::move` only).
- Reads: warehouse + item names.
- Cross-domain writes: none — receipts/adjustments/transfers reach this ledger by calling `StockService`, each writing only their own reference rows ([[../../../../security/data-ownership]]).

## Relations

- Consumes: same-domain `StockService::move` calls from warehouses (transfer), PO/GRN (receipt), adjustments, and e-commerce/sales when active.
- Feeds: nothing (no domain event — stock stays inside Operations).
- Shared entity: `ops_warehouses`.

## Related

- [[../_module|Inventory]] · [[./valuation|Valuation]] · [[../../warehouses/features/stock-transfer|Stock Transfer]]
