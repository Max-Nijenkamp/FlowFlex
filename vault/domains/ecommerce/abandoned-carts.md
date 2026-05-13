---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.abandoned-carts
status: planned
color: "#4ADE80"
---

# Abandoned Carts

> Track checkout sessions that did not convert, trigger automated recovery email sequences, and measure recovery conversion rates.

**Panel:** `ecommerce`
**Module key:** `ecommerce.abandoned-carts`

## What It Does

Abandoned Carts captures checkout sessions where a visitor added items to their cart and began checkout but did not complete the purchase. When an email address has been captured (at email step or from a signed-in customer), the module triggers a configurable automated email sequence with a link back to the pre-filled cart. Recovery rates, revenue recovered, and the optimal discount incentive to include are reported. This recovers an average of 5–15% of abandoned revenue without any manual effort.

## Features

### Core
- Cart tracking: capture cart contents and customer email as soon as the email step of checkout is completed
- Abandonment detection: if no purchase is recorded after a configurable time window (default 1 hour), the session is marked as abandoned
- Recovery email sequence: send up to 3 automated recovery emails at configurable intervals (e.g., 1 hour, 24 hours, 72 hours after abandonment)
- Recover cart link: each email includes a unique link that restores the exact cart contents at checkout
- Cart recovery status: pending, email sent, recovered (purchase completed), expired (not recovered within 14 days)
- Unsubscribe handling: recovery emails honour marketing opt-out preferences

### Advanced
- Discount incentive: configure whether to include a discount code in the second or third recovery email (e.g., 10% off if you complete in 24h)
- Segment-based sequences: send a different recovery sequence to first-time visitors vs returning customers vs VIP segment
- Cart value threshold: only trigger recovery emails for carts above a configurable minimum value (e.g., only for carts >€30)
- Recovery analytics: cart abandonment rate, recovery rate, revenue recovered, discount cost vs recovery value
- A/B test recovery sequences: test different subject lines, timings, or incentives for the recovery emails
- High-intent alert: flag carts with high-value items for manual follow-up by a sales rep instead of automated email

### AI-Powered
- Optimal discount amount: suggest the minimum discount needed to recover carts of a given value based on historical recovery data
- Best send time: predict the optimal recovery email timing per customer based on their past purchase behaviour

## Data Model

```erDiagram
    ec_abandoned_carts {
        ulid id PK
        ulid company_id FK
        ulid customer_id FK
        string session_id
        string customer_email
        json cart_contents
        decimal cart_value
        string recovery_status
        string recovery_link_token
        timestamp abandoned_at
        timestamp recovered_at
        timestamps timestamps
    }

    ec_cart_recovery_emails {
        ulid id PK
        ulid cart_id FK
        integer sequence_step
        string status
        decimal discount_offered
        timestamp scheduled_at
        timestamp sent_at
        boolean opened
        boolean clicked
    }

    ec_abandoned_carts ||--o{ ec_cart_recovery_emails : "triggers"
```

| Table | Purpose |
|---|---|
| `ec_abandoned_carts` | Abandoned checkout sessions with cart snapshot |
| `ec_cart_recovery_emails` | Recovery email sequence events per cart |

## Permissions

```
ecommerce.abandoned-carts.view-any
ecommerce.abandoned-carts.manage-sequences
ecommerce.abandoned-carts.view-analytics
ecommerce.abandoned-carts.export
ecommerce.abandoned-carts.delete
```

## Filament

**Resource class:** `AbandonedCartResource`
**Pages:** List, View
**Custom pages:** `RecoverySequenceConfigPage` (sequence timing and incentive configuration)
**Widgets:** `AbandonmentRateWidget`, `RecoveryRevenueWidget`
**Nav group:** Orders

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Klaviyo Abandoned Cart | Cart abandonment email sequences |
| Shopify Abandoned Checkout | Native cart recovery emails |
| Omnisend | Multi-step cart recovery automation |
| Drip | Ecommerce cart recovery workflows |

## Related

- [[orders]] — recovered carts create completed orders
- [[../marketing/email-marketing]] — recovery emails sent via marketing email module
- [[promotions]] — recovery discount codes generated from promotions module
- [[analytics]] — abandonment rate in ecommerce analytics funnel
