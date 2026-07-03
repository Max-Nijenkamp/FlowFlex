---
domain: ecommerce
module: orders
feature: fulfil-order
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Fulfil Order

Work the unfulfilled queue: mark lines shipped, record tracking, handle partial fulfilment; digital lines auto-fulfil.

## Behaviour

- Only `paid` orders are fulfillable.
- `OrderService::fulfil(FulfilData)` marks all or selected `line_ids` shipped, records a `tracking_number`, and updates `fulfilment_status` (`unfulfilled → partial → fulfilled`).
- When all physical lines are shipped, order → `fulfilled`; digital-only orders auto-fulfil on paid.
- Every step appends an `ec_order_events` row.

## UI

- **Kind**: custom-page (board)
- **Page**: "Fulfilment" (`/ecommerce/orders/fulfilment`), nav group **Orders** — `OrderFulfilmentPage`.
- **Layout**: a board/queue of paid, unfulfilled (or partially fulfilled) orders as cards/rows; each expands to its lines with quantity-to-ship inputs and a tracking field.
- **Key interactions**: select order → mark lines shipped + enter tracking → `fulfil` → card moves out of the queue (optimistic); partial ships leave the card with a "partial" badge.
- **States**: empty (nothing to fulfil → "all caught up" CTA) · loading (board skeleton) · error (toast + retry, e.g. order no longer paid) · selected (order expanded, lines editable).
- **Gating**: `ecommerce.orders.fulfil`.

## Data

- Owns / writes: `ec_orders` (`fulfilment_status`, `tracking_number`, `status`), `ec_order_events` only.
- Reads: `ec_order_lines` (what to ship).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: fulfilment completion may trigger a review-request mail (+7d) in [[../../reviews/_module|Reviews]] (reviews' own scheduled command reads fulfilled orders).
- Shared entity: none written cross-domain.

## Test Checklist

### Unit
- [ ] Fulfilment status resolves to `partial` when some (not all) physical lines are shipped, `fulfilled` when all are.
- [ ] Digital-only orders auto-fulfil on paid without a shipping step.

### Feature (Pest)
- [ ] Only `paid` orders are fulfillable — fulfilling a `pending` order is rejected.
- [ ] `fulfil` records tracking, appends an `ec_order_events` row, and transitions the order to `fulfilled` when all lines ship.
- [ ] Tenant B cannot fulfil tenant A's order.

### Livewire
- [ ] Fulfilment board action gated on `ecommerce.orders.fulfil`; denied without it.
- [ ] Marking shipped moves the card out of the queue (optimistic) and shows a "partial" badge on a partial ship.

## Unknowns

- Partial-fulfilment tracking: one tracking number per shipment vs per order (see [[../unknowns]]).

## Related

- [[../_module|Orders]] · [[place-order]] · [[../../reviews/_module|Reviews]]
