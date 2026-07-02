---
domain: ecommerce
module: payments
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payments — API / DTOs

## `RefundData`

| Field | Type | Rules |
|---|---|---|
| `payment_id` | ulid | must be `succeeded` |
| `amount_cents` | int | `≤ (amount_cents − refunded_amount_cents)` — "Refund exceeds the remaining amount." |
| `restock` | bool | restock order lines |

## `EcPaymentService`

- `createIntent(Order $o): array` — returns `{ payment_intent_id, client_secret }` for the checkout front end.
- `handleWebhook(array $event)` — dispatched from the webhook controller after signature verification.
- `refund(RefundData): void` — Stripe refund + `OrderService::refund`.

## Public / Portal Endpoints

| Route | Guard | Notes |
|---|---|---|
| `POST /webhooks/ecommerce/stripe` | public (no auth) | Stripe signature verified; `throttle:webhooks`; idempotent on intent id |

The checkout client secret is consumed by the storefront's Vue + Inertia checkout (Stripe.js), not a FlowFlex API.
