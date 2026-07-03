---
domain: ecommerce
module: abandoned-cart
feature: recover-cart
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Recover Cart

Detect abandoned carts, run the timed recovery-email sequence, and restore a cart via a signed one-click link — stopping on conversion.

## Behaviour

1. `CartRecoveryService::detect()` (every 15 min) flips `active` carts past the inactivity window (default 1h) to `abandoned`.
2. `advance()` sends due steps — 1st (1h), 2nd (24h), 3rd with discount (72h) — each once via a unique `(cart_id, step)` row.
3. The 3rd mail embeds a single-use coupon (promotions active).
4. A signed `recovery_token` link restores the session cart (`RestoreCartController`); a resulting order marks the cart `recovered`.
5. An order matching the cart email/token marks it `converted` and stops the sequence.
6. `PruneCartsCommand` deletes carts after 90 days.

## UI

- **Kind**: background (scheduled jobs + signed public link + read-only admin)
- **Page**: no interactive workflow page — `AbandonedCartResource` (read-only) + `CartRecoveryWidget` show the funnel; `RestoreCartController` handles the public link. Nav group **Marketing**.
- **Key interactions**: none admin-driven for the sequence (fully scheduled); shopper clicks the mail link → cart restored. Admin views recovery rate + revenue recovered.
- **States**: n/a for the job. Admin resource: empty (no abandoned carts) · loading (table) · error (toast). Public link: invalid/expired token → friendly "link expired" page.
- **Gating**: admin `ecommerce.abandoned-cart.view`; public restore = guest guard + signed URL + `throttle:public`.

## Data

- Owns / writes: `ec_carts`, `ec_cart_recovery_emails` only.
- Reads / Commands: `ec_orders` (conversion detection, read), promotions coupon service (single-use incentive, soft).
- Cross-domain writes: NONE — conversion is read from orders; the coupon is created through promotions' service; never writes order/coupon tables directly ([[../../../../security/data-ownership]]).

## Relations

- Consumes: cart snapshot from [[../../storefront/_module|storefront]] (checkout-start capture); order data from [[../../orders/_module|orders]] (read).
- Feeds: recovery link back into the storefront cart; recovered/converted status internal.
- Shared entity: `ec_orders` (read), coupons (promotions).

## Test Checklist

### Unit
- [ ] Inactivity window: active cart past 1h (default) flips abandoned; step schedule due-math (1h/24h/72h)

### Feature (Pest)
- [ ] Each step sends once (unique `(cart_id, step)` row) even across raced command runs
- [ ] Signed recovery token restores the session cart; resulting order marks `recovered`; matching order marks `converted` and stops the sequence
- [ ] Tenant isolation: recovery token never restores another company's cart; sends cite the notifications queue throttle

### Livewire
- [ ] `AbandonedCartResource` read-only with status + funnel; hidden without `ecommerce.abandoned-cart.view` or module inactive

## Unknowns

- SMS/WhatsApp channel; central suppression-list integration (see [[../unknowns]]).

## Related

- [[../_module|Abandoned Cart]] · [[../../storefront/_module|Storefront]] · [[../../promotions/_module|Promotions]]
