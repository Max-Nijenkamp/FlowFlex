---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.payments
status: planned
color: "#4ADE80"
---

# Payments

> Stripe-powered payment processing for the storefront, with support for multiple payment methods, refunds, and dispute management.

**Panel:** `ecommerce`
**Module key:** `ecommerce.payments`

## What It Does

Payments manages all money movement for the ecommerce storefront. Stripe is the primary payment processor, handling card authorisation, capture, and settlement. The module configures which payment methods are offered at checkout (card, iDEAL, Bancontact, SEPA, Apple Pay, Google Pay, Buy Now Pay Later), records each transaction against the originating order, processes refunds from within FlowFlex, and surfaces Stripe disputes for response. Payout summaries and reconciliation reports match Stripe settlements to FlowFlex order revenue.

## Features

### Core
- Stripe integration: connect Stripe account via API keys; all transactions processed through Stripe
- Payment methods: enable card (Visa, Mastercard, Amex), iDEAL, Bancontact, SEPA Direct Debit, Apple Pay, Google Pay per storefront and jurisdiction
- Checkout payment capture: authorise and capture at order placement or capture manually for pre-orders
- Transaction record: each payment linked to its order with amount, currency, Stripe payment intent ID, and status (pending/captured/failed/refunded)
- Refund processing: full or partial refund initiated from the order or transaction record; Stripe refund created automatically
- Failed payment handling: alert merchant; send customer a payment retry link

### Advanced
- Buy Now Pay Later: enable Klarna or Afterpay/Clearpay as checkout options (Stripe integration)
- Multi-currency: accept payments in multiple currencies; settle in the company's home currency via Stripe FX
- Payout tracking: see Stripe payout schedule and reconcile settled amounts against order revenue
- Dispute management: Stripe disputes surfaced in FlowFlex with evidence submission workflow and response deadline countdown
- 3D Secure: automatic 3DS challenge for applicable transactions (SCA compliance for EU/UK)
- Tax calculation: integrate Stripe Tax or TaxJar for automatic tax rate calculation at checkout

### AI-Powered
- Dispute win rate analysis: identify patterns in won vs lost disputes to improve evidence submission quality
- Payment method optimisation: recommend which payment methods to enable based on customer geography and cart abandonment rate

## Data Model

```erDiagram
    ec_payments {
        ulid id PK
        ulid company_id FK
        ulid order_id FK
        string stripe_payment_intent_id
        decimal amount
        string currency
        string payment_method
        string status
        timestamp captured_at
        timestamps timestamps
    }

    ec_refunds {
        ulid id PK
        ulid payment_id FK
        string stripe_refund_id
        decimal amount
        string reason
        string status
        ulid initiated_by FK
        timestamp refunded_at
    }

    ec_disputes {
        ulid id PK
        ulid payment_id FK
        string stripe_dispute_id
        decimal amount
        string reason
        string status
        date evidence_due_by
        json evidence_submitted
        timestamp resolved_at
    }

    ec_payments ||--o{ ec_refunds : "refunded via"
    ec_payments ||--o{ ec_disputes : "disputed via"
```

| Table | Purpose |
|---|---|
| `ec_payments` | Payment transactions linked to orders |
| `ec_refunds` | Refund records with Stripe refund ID |
| `ec_disputes` | Stripe chargebacks and dispute workflow |

## Permissions

```
ecommerce.payments.view-any
ecommerce.payments.refund
ecommerce.payments.manage-disputes
ecommerce.payments.configure-methods
ecommerce.payments.view-payouts
```

## Filament

**Resource class:** `PaymentResource`, `DisputeResource`
**Pages:** List, View
**Custom pages:** `DisputeResponsePage` (evidence submission interface with deadline countdown)
**Widgets:** `PaymentSummaryWidget` (today's revenue, pending payouts), `OpenDisputesWidget`
**Nav group:** Orders

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Payments | Native payment processing |
| WooCommerce Stripe plugin | Stripe payment integration |
| Mollie | Multi-method European payment processing |
| PayPal Commerce | Multi-method payment processing |

## Related

- [[orders]] — each order has one or more payment records
- [[returns]] — refunds initiated from return workflow
- [[subscriptions]] — recurring billing handled through Stripe subscriptions
- [[../finance/INDEX]] — payment settlements reconciled against finance revenue
