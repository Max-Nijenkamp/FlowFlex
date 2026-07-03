---
domain: ecommerce
module: storefront
feature: checkout
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Checkout

Turn a validated cart into an order: apply discounts, place via `OrderService`, pay via Stripe (when active), confirm.

## Behaviour

1. Checkout re-validates cart stock/prices server-side.
2. Applies `DiscountEngine::apply` (soft) and tax (finance.tax, soft).
3. Collects customer + shipping per `checkout_config` (guest toggle, required fields, terms).
4. Posts `CreateOrderData` → `OrderService::place` (orders module).
5. Payments active → `EcPaymentService::createIntent` + Stripe.js confirm; else order placed `pending` for manual mark-paid.
6. Confirmation page; a DB cart may have been captured at checkout start for abandoned-cart recovery.

## UI

- **Kind**: public-vue
- **Page**: `Shop/Checkout.vue` (`/shop/{slug}/checkout`) + `Shop/Confirmation.vue` (`/shop/{slug}/confirmation/{order}`).
- **Layout**: single-page or stepped — contact/shipping, then payment (Stripe element), order summary with discount + tax + shipping lines.
- **Key interactions**: enter details → apply coupon (server-validated) → confirm payment → order placed → confirmation. Guest checkout honored per settings.
- **States**: empty (empty cart blocks checkout) · loading (placing / confirming payment) · error (stale price/stock re-validate; coupon rejection message; payment failure → retry, order stays pending) · selected (payment method chosen).
- **Gating**: public/guest guard, company-scoped.

## Data

- Owns / writes: session cart; `ec_carts` capture is owned by [[../../abandoned-cart/_module|Abandoned Cart]].
- Reads / Commands: `OrderService::place` (orders), `EcPaymentService::createIntent` (payments), `DiscountEngine::apply` (promotions), tax classes (finance.tax).
- Cross-domain writes: NONE — order, payment, and redemption rows are written by their owning services; storefront never writes `ec_orders`/`ec_payments`/coupon tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: cart from [[browse-and-cart]].
- Feeds: `OrderService::place` → order → `CheckoutCompleted` → Finance.
- Shared entity: `ec_orders`, `ec_payments`, coupons — all owned elsewhere.

## Test Checklist

### Unit
- [ ] `CreateOrderData` built from validated cart; checkout_config drives required fields/guest toggle

### Feature (Pest)
- [ ] Server-side re-validation rejects stale stock/prices before placing; oversell prevented by orders-module lock (concurrent checkouts of last unit -> one succeeds)
- [ ] Payments soft-dep: active -> Stripe intent + confirm; inactive -> order placed `pending`
- [ ] Discount + tax soft-deps degrade gracefully when inactive; public checkout rate-limited *(assumed guest limiter -- registry reconcile task #7)*

### Livewire
- (none -- public Vue + Inertia flow)

## Unknowns

- Account vs guest checkout / customer portal ownership (see [[../unknowns]]).

## Related

- [[../_module|Storefront]] · [[browse-and-cart]] · [[../../orders/features/place-order|Place Order]] · [[../../payments/_module|Payments]]
