---
domain: ecommerce
module: products
feature: stock-linkage
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Stock Linkage

The single `ProductStock` API that bridges a product to either `operations.inventory` stock or an internal `stock_quantity` field — so orders/storefront never branch on the source.

## Behaviour

- A product carries either `ops_item_id` (operations-backed) **or** an internal `stock_quantity` — mutually exclusive.
- `ProductStock::available(product, variant?)` returns the current sellable count from whichever source is set.
- `reserve / release / deduct` mutate stock: when ops-backed they call `StockService`; otherwise they adjust the internal field.
- Orders reserve on placement, deduct on paid, release on cancel — all through `ProductStock`, never touching `ops_*` tables.

## UI

- **Kind**: background (no dedicated screen — surfaced as a field/badge inside `EcProductResource`).
- **Page**: none. The link is set via the Inventory section of the product edit form (item picker or manual quantity); a stock badge shows current availability.
- **Key interactions**: choose "track via Operations" (item picker) or "internal quantity" (number field). Read-only availability badge derived from `ProductStock::available`.
- **States**: n/a (no standalone page). Out-of-stock shows a badge on the product row.
- **Gating**: edited under `ecommerce.products.update`.

## Data

- Owns / writes: internal `stock_quantity` on `ec_products` only.
- Reads / Commands: `operations.inventory` `StockService::available/reserve/release/deduct` when `ops_item_id` is set (read + command through the owning service).
- Cross-domain writes: NONE — ops stock is only ever changed through `StockService`, never by writing `ops_*` tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing event-wise (synchronous service calls).
- Feeds: availability + reservation used by [[../../orders/_module|Orders]] and [[../../storefront/_module|Storefront]].
- Shared entity: `ops_items` / stock ledger owned by `operations.inventory`.

## Test Checklist

### Unit
- [ ] `ProductStock::available` returns the internal `stock_quantity` when `ops_item_id` is null.
- [ ] `ops_item_id` and `stock_quantity` are mutually exclusive — setting both is rejected.

### Feature (Pest)
- [ ] `reserve`/`deduct`/`release` on an ops-backed product route to `StockService` and never write `ops_*` tables directly.
- [ ] Internal `stock_quantity` decrement under concurrent reserve uses a row lock so two orders cannot oversell the last unit.
- [ ] Availability for a product linked to a deactivated ops item resolves to zero *(assumed)*.

## Unknowns

- Behaviour when an `ops_item_id` points at a deactivated operations item *(assumed: treated as zero stock)* — unconfirmed.

## Related

- [[../_module|Product Catalogue]] · [[manage-catalogue]] · [[../../../operations/inventory/_module|Inventory]]
