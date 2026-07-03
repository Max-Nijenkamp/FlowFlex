---
domain: ecommerce
module: payments
feature: process-payment
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Process Payment

Create a Stripe Payment Intent at checkout, then reconcile via webhook to drive the order to `paid`.

## Behaviour

1. `EcPaymentService::createIntent(order)` returns a client secret; the storefront confirms it with Stripe.js.
2. Stripe fires `payment_intent.succeeded` → webhook (signature-verified) records an `ec_payments` row and calls `OrderService::markPaid`.
3. `payment_intent.payment_failed` → payment row marked `failed`; order stays `pending` with a retry link.
4. Replaying the same intent is idempotent (unique intent id + status guard).

## UI

- **Kind**: background (webhook handler) + read-only resource
- **Page**: no interactive page — `POST /webhooks/ecommerce/stripe` handles it; payments are viewed on `EcPaymentResource` (read-only) and on the order view.
- **Key interactions**: none user-driven server-side; the payment step itself is Stripe.js on the storefront checkout.
- **States**: n/a (background). Order view shows payment status badge (pending/succeeded/failed).
- **Gating**: viewing `ecommerce.payments.view-any`; webhook route is public + signature-verified + `throttle:webhooks`.

## Data

- Owns / writes: `ec_payments` only.
- Reads / Commands: `OrderService::markPaid` (orders); Stripe API.
- Cross-domain writes: NONE — order state changes go through `OrderService`, never by writing `ec_orders` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: Stripe webhook (external), not a FlowFlex event.
- Feeds: drives `OrderService::markPaid` → which fires `CheckoutCompleted` to Finance.
- Shared entity: `ec_orders` (owned by orders, reached via service).

## Test Checklist

### Unit
- [ ] Webhook event dispatch maps `payment_intent.succeeded` → markPaid path and `payment_intent.payment_failed` → failed path.

### Feature (Pest)
- [ ] Bad Stripe signature → 400, no payment row written, order unchanged.
- [ ] Valid `succeeded` webhook writes an `ec_payments` row and calls `OrderService::markPaid` (Stripe mocked via `Http::fake`/stripe-mock).
- [ ] Replaying the same `payment_intent` id is idempotent — no duplicate row, no double markPaid (unique intent id + status guard, under row lock).
- [ ] Failed payment records `failed` and leaves the order `pending`; webhook resolves the correct company context (tenant isolation).

## Unknowns

- Retry expiry policy for failed payments (see [[../unknowns]]).

## Related

- [[../_module|Payments]] · [[refund]] · [[../../orders/_module|Orders]]
