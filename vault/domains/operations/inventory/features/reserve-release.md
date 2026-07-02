---
domain: operations
module: inventory
feature: reserve-release
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Reserve & Release

Hold stock for a pending sale/cart without removing it from on-hand.

## Behaviour

- `StockService::reserve(item, warehouse, qty)` raises `quantity_reserved`; `available = on_hand − reserved` drops.
- `StockService::release(...)` lowers `quantity_reserved`.
- On-hand changes only on an actual `out` (fulfilment) movement.
- Reserving beyond available is rejected.
- Primary callers are sales / e-commerce (when those modules are active) — this feature exposes the contract; no dedicated screen.

## UI

- **Kind**: background — no page; a service contract consumed by sales/e-commerce. Reserved quantities are *displayed* on the item view + stock board, but reserve/release is not a manual screen in v1.
- **Trigger**: `StockService::reserve` / `release` calls from sales-order / cart flows (soft dependents).

## Data

- Owns / writes: `ops_stock_levels` (`quantity_reserved`) via `StockService` only.
- Reads: current level for availability check.
- Cross-domain writes: none — sales/e-commerce call `StockService`, they never write levels directly ([[../../../../security/data-ownership]]).

## Relations

- Consumes: reserve/release calls from ecommerce / sales-orders (same pattern as other stock mutators).
- Feeds: nothing (no event).
- Shared entity: none beyond `ops_items` / `ops_stock_levels` it owns.

## Related

- [[../_module|Inventory]] · [[./stock-movements|Stock Movements]]
