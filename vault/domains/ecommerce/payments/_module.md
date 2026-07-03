---
domain: ecommerce
module: payments
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Payments

Stripe payment processing for orders: checkout intents, capture, refunds, and webhook reconciliation.

## Module-key

`ecommerce.payments`

**Priority:** p3  
**Panel:** ecommerce  
**Permission prefix:** `ecommerce.payments`  
**Tables:** `ec_payments`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../orders/_module\|Orders]] | payments belong to orders; success drives `markPaid` |
| Hard | [[../../core/billing/_module\|Billing]] · [[../../core/rbac/_module\|RBAC]] | gating + permissions |

Uses the company-level Stripe account config — connected account per company *(assumed: Stripe Connect or per-company keys — build-time ADR)*.

## Core Features

- **Payment Intents** for storefront orders; capture on placement.
- **Webhook sync** — `payment_intent.succeeded/failed`, signature-verified.
- **Refunds** — full/partial through Stripe; drives the order refund flow.
- **Saved methods** — Stripe customer objects for returning buyers; **no card data stored locally**.
- **Methods** — card, iDEAL, SEPA (EU-relevant).
- **Idempotency keys** on all Stripe mutations.
- **Failed payment** — order stays `pending`, retry link.

## See features/

- [[features/process-payment|Process Payment]] — intent → webhook → `markPaid`.
- [[features/refund|Refund]] — full/partial Stripe refund + order refund flow.

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

## Test Checklist

- [ ] Tenant isolation: a webhook/refund resolves to the owning company; company A cannot view or refund company B payments.
- [ ] Module gating: payment resource hidden when `ecommerce.payments` inactive.
- [ ] Webhook bad signature → 400, no change; success → order paid.
- [ ] Webhook replay (same intent) idempotent.
- [ ] Partial refunds capped at remaining; tracked cumulative.
- [ ] Stripe mocked (`Http::fake` / stripe-mock).
- [ ] Failed payment leaves order pending.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | `OrderService::markPaid` / `refund` | ecommerce.orders | Payment success/refund drives order state; payments never writes `ec_orders` |
| External | Stripe API + webhook | Stripe | Signature-verified; idempotency keys |

**Data ownership:** `ecommerce.payments` writes only `ec_payments`. It drives order state through `OrderService`, never by writing `ec_orders` directly. No card data is stored locally — only Stripe references ([[../../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../orders/_module|Orders]] · [[../../../../build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk]] · [[../../../../architecture/security]]
- [[../../../glossary]]
