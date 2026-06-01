---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.payments
status: planned
color: "#4ADE80"
---

# Payments

Stripe payment processing for orders: checkout, payment capture, refunds, and webhook reconciliation.

## Core Features

- Stripe Checkout / Payment Intents for storefront orders
- Payment capture on order placement
- Payment status sync via Stripe webhook (verified — see [[architecture/security]])
- Refund processing (full/partial) through Stripe
- Saved payment methods for returning customers
- Multiple payment methods: card, iDEAL, SEPA (EU-relevant)
- Payment record per order with Stripe references
- Failed payment handling

## Data Model

| Table | Key Columns |
|---|---|
| `ec_payments` | company_id, order_id, stripe_payment_intent_id, amount_cents, currency, status, method, paid_at, refunded_amount_cents |

## Filament

**Nav group:** Orders

- `PaymentResource` — read-only payment records, refund action
- Payment status shown on order view

## Cross-Domain / Security

- Uses raw `stripe/stripe-php` (not Cashier — see [[build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk]])
- Webhook signature verification mandatory (see [[architecture/security]])

## Related

- [[domains/ecommerce/orders]]
- [[domains/finance/invoicing]]
