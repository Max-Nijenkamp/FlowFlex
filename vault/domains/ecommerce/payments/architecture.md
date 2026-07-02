---
domain: ecommerce
module: payments
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy | Notes |
|---|---|---|---|
| `EcPaymentResource` | Orders | simple-resource (read-only) | refund action; status shown on order view |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.payments.view-any')
        && BillingService::hasModule('ecommerce.payments');
}
```

## Jobs & Scheduling

None (webhook-driven, synchronous handler dispatching order commands).

## Search & Realtime

None.
