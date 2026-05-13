---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.subscriptions
status: planned
color: "#4ADE80"
---

# Subscriptions

> Recurring product subscriptions with configurable billing cycles, subscriber self-service, pause and resume, and automated dunning for failed payments.

**Panel:** `ecommerce`
**Module key:** `ecommerce.subscriptions`

## What It Does

Subscriptions enables merchants to sell physical or digital products on a recurring basis from the storefront. Customers subscribe to receive a product at a chosen interval (weekly, monthly, quarterly) at a discounted subscribe-and-save price. A subscriber self-service portal lets customers skip a delivery, pause for up to 3 months, update their payment method, change their delivery address, or cancel. Failed payments trigger a dunning sequence before the subscription is suspended. All billing is handled through Stripe via the payments module.

## Features

### Core
- Subscription product configuration: recurring intervals (weekly, every 2 weeks, monthly, quarterly, annual), subscriber discount percentage, minimum commitment period (e.g., 3 months before cancel), trial period (days free or reduced price)
- Storefront checkout: one-time purchase and subscribe-and-save options shown side-by-side on product page
- Subscriber record: linked customer, product, status (active, paused, cancelled), next order date, payment method
- Recurring order generation: on each billing cycle, automatically create an order and charge the customer via Stripe
- Skip a delivery: subscriber can skip the next order without cancelling; next order date advances by one interval
- Cancellation: subscriber can cancel; billing stops; retention offer shown before confirmation

### Advanced
- Subscriber self-service portal: embedded customer portal for all subscription management without contacting support
- Pause subscription: pause for 1–3 months; no charges during pause; auto-resumes on the configured date
- Address and payment method update: subscriber can update delivery address and payment card from the portal
- Dunning sequence: on failed payment, retry on day 3, day 7, day 14; notify subscriber to update card; suspend after final retry
- Subscription analytics: active subscribers, monthly recurring revenue (MRR) from subscriptions, churn rate, average subscriber lifetime
- Cohort retention: track subscriber retention by acquisition month

### AI-Powered
- Churn prediction: identify subscribers at risk of cancellation based on skip frequency and engagement decline
- Retention offer optimisation: suggest the right discount or pause option to present at the moment of cancellation intent

## Data Model

```erDiagram
    ec_subscription_products {
        ulid id PK
        ulid product_variant_id FK
        string interval
        decimal discount_pct
        integer minimum_commitment_months
        integer trial_days
        timestamps timestamps
    }

    ec_subscriptions {
        ulid id PK
        ulid company_id FK
        ulid customer_id FK
        ulid subscription_product_id FK
        string status
        date next_order_date
        date paused_until
        date cancelled_on
        string stripe_subscription_id
        timestamps timestamps
    }

    ec_subscription_orders {
        ulid id PK
        ulid subscription_id FK
        ulid order_id FK
        integer cycle_number
        string status
        timestamp billed_at
    }

    ec_subscription_products ||--o{ ec_subscriptions : "powers"
    ec_subscriptions ||--o{ ec_subscription_orders : "generates"
```

| Table | Purpose |
|---|---|
| `ec_subscription_products` | Subscription configuration per product variant |
| `ec_subscriptions` | Active subscriber records with status and billing dates |
| `ec_subscription_orders` | Recurring orders generated per billing cycle |

## Permissions

```
ecommerce.subscriptions.view-any
ecommerce.subscriptions.manage-products
ecommerce.subscriptions.manage-subscribers
ecommerce.subscriptions.cancel
ecommerce.subscriptions.view-analytics
```

## Filament

**Resource class:** `SubscriptionProductResource`, `SubscriberResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `SubscriberPortalPage` (self-service portal interface), `SubscriptionAnalyticsPage`
**Widgets:** `ActiveSubscribersWidget`, `MrrWidget`, `ChurnRateWidget`
**Nav group:** Marketing

## Displaces

| Competitor | Feature Replaced |
|---|---|
| ReCharge | Shopify subscription management |
| Bold Subscriptions | Recurring ecommerce subscriptions |
| Skio | Subscription billing and dunning |
| Ordergroove | Subscribe-and-save programme |

## Related

- [[products]] — subscription products built on top of standard product variants
- [[payments]] — recurring Stripe charges managed through the payments module
- [[orders]] — each billing cycle creates a standard order
- [[analytics]] — subscription MRR and churn tracked in ecommerce analytics
