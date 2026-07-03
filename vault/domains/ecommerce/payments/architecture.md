---
domain: ecommerce
module: payments
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Payments — Architecture

## Payment Lifecycle

```
create intent (checkout) → pending
   │  webhook payment_intent.succeeded
   ▼
succeeded ──refund──> (refunded_amount tracked; order refund flow)
   │  webhook payment_intent.payment_failed
   ▼
failed (order stays pending; retry link)
```

`ec_payments.status` is a plain enum (`pending/succeeded/failed`) — no state-machine class *(assumed)*.

## Services & Actions

| Method | Responsibility |
|---|---|
| `EcPaymentService::createIntent(Order $o): array` | PaymentIntent + client secret for checkout |
| `EcPaymentService::handleWebhook(array $event)` | `payment_intent.succeeded` → payment row + `OrderService::markPaid`; failures recorded |
| `EcPaymentService::refund(RefundData)` | Stripe refund with idempotency key + order refund flow |

Uses `stripe/stripe-php` raw SDK (not Cashier — see [[../../../../build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk]]). All mutations carry idempotency keys.

## Events

None fired/consumed on the FlowFlex event bus — payments drives orders via `OrderService` command calls. Stripe webhooks are the external trigger. See [[../../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Orders

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `EcPaymentResource` | #1 CRUD resource | tweaks: read-only-flow-owned (writes owned by `EcPaymentService` / Stripe webhook — `canCreate(): false`), custom-header-actions (refund) | status badge; refund modal; surfaced on the order view too |

**Public webhook (not a Filament artifact):**

- `POST /webhooks/ecommerce/stripe` — Stripe signature-verified, `throttle:webhooks`, idempotent on intent id (see [[./security]] and [[./api]]). Public route, no panel gate.

**Access contract (mandatory):** the resource gates on
`canAccess() = Auth::user()->can('ecommerce.payments.view-any') && BillingService::hasModule('ecommerce.payments')`
per [[../../../architecture/filament-patterns]] #1. The resource is read-only — all writes flow from `EcPaymentService` (Stripe webhook + refund action); the refund action additionally requires `ecommerce.payments.refund`. The Stripe webhook is a public signed endpoint, not a Filament surface.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Webhook `payment_intent.succeeded` → payment row + `markPaid` | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the payment/order rows; unique `stripe_payment_intent_id` + status guard make replays idempotent (a second delivery of the same intent is a no-op) |
| `refund` — `refunded_amount_cents` accumulation | Pessimistic | `DB::transaction()` + `lockForUpdate()` on `ec_payments`, re-read remaining, validate cap, write — prevents two concurrent refunds together exceeding the captured amount (money mutation) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None (webhook-driven, synchronous handler dispatching order commands).

## Search & Realtime

None.
