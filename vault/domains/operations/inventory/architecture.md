---
domain: operations
module: inventory
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ItemResource` | #1 CRUD resource | tweaks: view-page-tabs, inline-relation-repeater (per-warehouse levels, read-only display) | SKU search + category filter; low-stock badge column |
| `StockMovementResource` | #1 CRUD resource | tweak: read-only-flow-owned (writes owned by `StockService::move`) | append-only ledger history; filters by item / warehouse / type / date |
| `StockBoardPage` | #18 heat-map / matrix custom page | [[../../../architecture/patterns/page-blueprints#Heat-map / Matrix]] *(assumed — items × warehouses availability matrix; described in [[./features/stock-movements]] but absent from Build Manifest, see QUESTIONS)* | cell = available; row action "record movement" (manual `move`) |
| `LowStockWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | items below reorder point; polling 30–60s |
| `ValuationWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] *(assumed — described in [[./features/valuation]] but absent from Build Manifest, see QUESTIONS)* | total + by-warehouse stock value |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('operations.inventory.view-any') && BillingService::hasModule('operations.inventory')`
per [[../../../architecture/filament-patterns]] #1. `StockBoardPage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages; its manual-move action additionally requires `operations.inventory.move-stock`. No public/portal surfaces — the `operations` panel is authenticated only.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Item CRUD (`ItemResource` form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Stock move (`StockService::move`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the `ops_stock_levels` row, re-read available, validate `out`/`transfer-out` ≤ available, then write movement + upsert level — the inventory decrement path per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]] |
| Reserve / release (`StockService::reserve` / `release`) | Pessimistic | `lockForUpdate()` on the level row inside a transaction; check available before raising `quantity_reserved` |
| Movement ledger (`ops_stock_movements`) | n/a (append-only) | Ledger rows are immutable — no update/delete path |
| Valuation / low-stock reads | n/a (read-only) | Derived computations over levels; no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

- Meilisearch (Scout): items indexed on `sku`, `name`, `category` for global + panel search *(assumed)*.
- No realtime on level changes in v1 — the movement ledger is read on load *(assumed)*.
