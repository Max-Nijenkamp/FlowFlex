---
type: module
domain: E-Commerce
panel: ecommerce
phase: 3
status: planned
cssclasses: domain-ecommerce
migration_range: 756000–756499
last_updated: 2026-05-09
---

# Subscription Products

Sell physical or digital products on a recurring subscription basis from the e-commerce storefront. Subscribe-and-save, subscription boxes, digital content subscriptions.

---

## Subscription Models

| Model | Example |
|---|---|
| Subscribe & save | Coffee beans, monthly 10% off |
| Fixed box | Curated monthly box, same every month |
| Variable box | Monthly box, contents change |
| Digital subscription | SaaS product or content library access |
| Prepaid | Pay for 12 months, delivered monthly |

---

## Subscription Setup

Per product:
- Recurring intervals: weekly / every 2 weeks / monthly / quarterly / annually
- Subscriber discount (e.g., 10% vs one-time price)
- Minimum commitment: 3 months minimum before cancel
- Skip option: subscriber can skip a delivery
- Pause option: pause for up to 3 months
- Trial: first month free / 14-day trial

---

## Subscriber Management

Self-service subscriber portal:
- Change delivery frequency
- Skip next order
- Pause subscription
- Update payment method
- Update delivery address
- Cancel (with retention offer: "stay for 10% off next 3 months")

---

## Billing Integration

Links to Subscription Billing module ([[recurring-billing-engine]]):
- Recurring charge on schedule
- Failed payment → dunning sequence
- Subscription pause → billing paused
- Cancellation → billing stopped, access revoked

---

## Subscription Analytics

- Active subscribers, subscriber growth
- Monthly recurring revenue from subscriptions
- Churn rate: cancellations per month
- Average subscription lifetime
- LTV vs CAC for subscription products

---

## Data Model

### `ec_subscription_products`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| product_id | ulid | FK |
| interval | enum | weekly/biweekly/monthly/quarterly/annual |
| discount_pct | decimal(5,2) | |
| minimum_commitment | int | months |
| trial_days | int | nullable |

### `ec_subscriber_orders`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| customer_id | ulid | FK |
| sub_product_id | ulid | FK |
| status | enum | active/paused/cancelled |
| next_order_date | date | |
| cancellation_date | date | nullable |

---

## Migration

```
756000_create_ec_subscription_products_table
756001_create_ec_subscriber_orders_table
```

---

## Related

- [[MOC_Ecommerce]]
- [[MOC_SubscriptionBilling]]
- [[promotions-discount-engine]]
