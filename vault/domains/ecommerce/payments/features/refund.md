---
domain: ecommerce
module: payments
feature: refund
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Refund

Issue a full or partial Stripe refund against a succeeded payment and drive the order's refund flow.

## Behaviour

- `EcPaymentService::refund(RefundData)` — only on a `succeeded` payment.
- `amount_cents` must be `≤ (amount_cents − refunded_amount_cents)`; over-refund is rejected with "Refund exceeds the remaining amount."
- Stripe refund carries an idempotency key; `refunded_amount_cents` accumulates.
- Calls `OrderService::refund(amount_cents, restock)`; `restock` returns stock via `ProductStock`.

## UI

- **Kind**: simple-resource (row action)
- **Page**: "Refund" action on `EcPaymentResource` / order view (`/ecommerce/payments`), nav group **Orders**.
- **Layout**: refund modal — remaining-refundable amount shown, amount input (defaults to remaining), restock toggle.
- **Key interactions**: click "Refund" → modal → confirm → Stripe refund → order refund flow → payment row updates cumulative refunded amount.
- **States**: empty (n/a) · loading (processing refund) · error (over-refund message; Stripe failure toast) · selected (payment row).
- **Gating**: `ecommerce.payments.refund`.

## Data

- Owns / writes: `ec_payments` (`refunded_amount_cents`) only.
- Reads / Commands: `OrderService::refund` (orders), Stripe API.
- Cross-domain writes: NONE — order/stock changes go through their owning services ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `OrderService::refund` → order → `refunded` state; optional restock.
- Shared entity: `ec_orders` (orders), stock (operations via `ProductStock`).

## Test Checklist

### Unit
- [ ] Remaining refundable = `amount_cents − refunded_amount_cents`; an amount above it is rejected.
- [ ] Refund allowed only on a `succeeded` payment.

### Feature (Pest)
- [ ] A partial refund accumulates `refunded_amount_cents` and calls `OrderService::refund` with the restock flag (Stripe mocked, idempotency key sent).
- [ ] Two concurrent refunds cannot together exceed the captured amount (row lock on `ec_payments`).
- [ ] Refund denied without `ecommerce.payments.refund`; tenant B cannot refund tenant A's payment.

### Livewire
- [ ] Refund modal defaults the amount to the remaining refundable and shows the over-refund error inline instead of submitting.
- [ ] Refund action gated on `ecommerce.payments.refund`; hidden without it.

## Unknowns

- Whether a partial refund emits a credit-note event to Finance (see [[../../orders/unknowns]]).

## Related

- [[../_module|Payments]] · [[process-payment]] · [[../../orders/_module|Orders]]
