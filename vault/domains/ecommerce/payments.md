---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.payments
status: planned
priority: p3
depends-on: [ecommerce.orders, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [money]
tables: [ec_payments]
permission-prefix: ecommerce.payments
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Payments

Stripe payment processing for orders: checkout, payment capture, refunds, and webhook reconciliation.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ecommerce/orders\|ecommerce.orders]] | payments belong to orders; success drives `markPaid` |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

(Uses the company-level Stripe account config — connected account per company *(assumed: Stripe Connect or per-company keys — build-time ADR)*.)

---

## Core Features

- Stripe Payment Intents for storefront orders
- Payment capture on order placement
- Payment status sync via Stripe webhook (signature-verified — [[architecture/security]])
- Refund processing (full/partial) through Stripe — drives order refund flow
- Saved payment methods for returning customers (Stripe customer objects, no card data stored locally)
- Payment methods: card, iDEAL, SEPA (EU-relevant)
- Payment record per order with Stripe references
- Failed payment handling: order stays pending, retry link
- Idempotency keys on all Stripe mutations ([[architecture/queue-jobs]] money rule)

---

## Data Model

### ec_payments

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), order_id FK | ulid | |
| stripe_payment_intent_id | string unique | |
| amount_cents | bigint | |
| currency | string(3) | |
| status | string | pending / succeeded / failed |
| method | string nullable | card / ideal / sepa |
| paid_at | timestamp nullable | |
| refunded_amount_cents | bigint default 0 | ≤ amount |

---

## DTOs

### RefundData — payment_id (succeeded), amount_cents (≤ remaining — "Refund exceeds the remaining amount."), restock (bool)

## Services & Actions

- `EcPaymentService::createIntent(Order $o): array` — PaymentIntent + client secret for checkout
- `EcPaymentService::handleWebhook(array $event)` — `payment_intent.succeeded` → payment row + `OrderService::markPaid`; failures recorded
- `EcPaymentService::refund(RefundData)` — Stripe refund w/ idempotency key + order refund flow

---

## Filament

**Nav group:** Orders

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EcPaymentResource` | #1 (read-only) | refund action; status on order view |

---

## Permissions

`ecommerce.payments.view-any` · `ecommerce.payments.refund`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Webhook bad signature → 400, no change; success → order paid
- [ ] Webhook replay (same intent) idempotent
- [ ] Partial refunds capped at remaining; tracked cumulative
- [ ] Stripe mocked (`Http::fake` / stripe-mock)
- [ ] Failed payment leaves order pending

---

## Build Manifest

```
database/migrations/xxxx_create_ec_payments_table.php
app/Models/Ecommerce/EcPayment.php
app/Data/Ecommerce/RefundData.php
app/Services/Ecommerce/EcPaymentService.php
app/Http/Controllers/Webhooks/EcStripeWebhookController.php
app/Filament/Ecommerce/Resources/EcPaymentResource.php
database/factories/Ecommerce/EcPaymentFactory.php
tests/Feature/Ecommerce/{PaymentWebhookTest,RefundTest}.php
```

---

## Open Questions

- Stripe Connect (platform collects, pays out) vs per-company Stripe keys — **build-time ADR required** *(affects fees + onboarding)*

---

## Related

- [[domains/ecommerce/orders]]
- [[build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk]]
- [[architecture/security]]
