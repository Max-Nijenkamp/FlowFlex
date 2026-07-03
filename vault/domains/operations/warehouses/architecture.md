---
domain: operations
module: warehouses
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Warehouses — Architecture

## Services & Actions

No multi-method service — the only non-CRUD operation is the transfer, modelled as a `lorisleiva/laravel-actions` action.

| Artifact | Notes |
|---|---|
| `TransferStockAction::run(CreateTransferData $data): WarehouseTransfer` | one DB transaction: transfer row + two `StockService::move` calls (`transfer-out` at source, `transfer-in` at destination). Source availability checked before the pair. Both movements commit together or neither does. |

Default-warehouse invariant (exactly one `is_default = true` per company) is enforced in the `WarehouseResource` save path / a small `SetDefaultWarehouseAction` *(assumed)* — setting a new default unsets the prior one in the same transaction.

---

## Events

None fired, none consumed. Transfers are an intra-domain stock operation; they produce two `ops_stock_movements` rows via `StockService` (owned by inventory) but emit no domain event.

---

## Filament Artifacts

**Nav group:** Inventory

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WarehouseResource` | #1 CRUD resource | default toggle; address form group |
| `WarehouseTransferResource` | #1 CRUD resource | create form (source, dest, item, qty) + read-only history |

**Access contract:** every artifact gates on `canAccess() = Auth::user()->can('operations.warehouses.view-any') && BillingService::hasModule('operations.warehouses')` per [[../../../architecture/filament-patterns]] #1.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Warehouse CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| Set default warehouse | Pessimistic | Unset-prior + set-new in one transaction with `lockForUpdate` -- exactly one `is_default` per company |
| `TransferStockAction` | Pessimistic | One DB transaction; `StockService::move` locks stock rows (`lockForUpdate`) for the out/in pair -- availability check + both movements atomic (inventory-capacity decrement tier) |
| Transfer history reads | n-a | Read-only |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

- No Meilisearch index in v1 *(assumed)* — warehouse count per company is small.
- No realtime — transfers are instant CRUD.
