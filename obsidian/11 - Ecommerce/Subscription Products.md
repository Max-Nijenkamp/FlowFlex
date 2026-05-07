---
tags: [flowflex, domain/ecommerce, subscriptions, recurring, phase/4]
domain: Ecommerce
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-07
---

# Subscription Products

Recurring product sales with Stripe Subscriptions, full dunning management, and customer self-service. Sell SaaS, boxes, memberships, or any recurring billing product.

**Who uses it:** Ecommerce team, finance, customers (self-service portal)
**Filament Panel:** `ecommerce`
**Depends on:** [[Product Catalogue]], [[Storefront & Checkout]], Stripe
**Phase:** 4
**Build complexity:** High — 4 resources, 2 pages, 4 tables

---

## Features

- **Subscription plan builder** — define plans with name, price, billing cycle (monthly/quarterly/annual), trial period, and link to an `ec_product`
- **Stripe Subscriptions integration** — plans map to Stripe price objects; subscriptions created via Stripe API; webhooks update local status
- **Trial period support** — configurable trial days; customer not charged until trial ends; trial reminder email before billing
- **Customer subscription dashboard** — customers view their active subscriptions, next billing date, amount, and can cancel or pause via self-service portal
- **Subscription status lifecycle** — trialing → active → past_due → cancelled/expired; status updated by Stripe webhook
- **Dunning management** — automatic retry schedule for failed payments: attempt 1 (day 3), attempt 2 (day 7), attempt 3 (day 14); `PaymentFailed` event fires on each failure
- **Smart dunning communications** — each dunning attempt triggers a different email tone: friendly reminder → urgent → final warning before cancellation
- **Cancellation flow** — customer selects cancellation reason; offer pause or downgrade before confirming cancel; `SubscriptionCancelled` event fires
- **Renewal notifications** — `SubscriptionRenewed` event on successful renewal; email receipt to customer
- **Revenue metrics widget** — MRR, ARR, churn rate, new subscriptions this month; dashboard widget in `ecommerce` panel
- **Proration handling** — upgrade/downgrade mid-cycle; prorated amount computed and charged/credited via Stripe
- **`SubscriptionStarted` event** — triggers welcome sequence in [[Email Marketing]] and creates CRM contact activity
- **Bulk subscription management** — filter by status, bulk cancel, bulk pause; export subscriber list

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `subscription_plans`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `price` | decimal(10,2) | |
| `currency` | string(3) default 'GBP' | |
| `billing_cycle` | enum | `monthly`, `quarterly`, `annual` |
| `trial_days` | integer default 0 | |
| `ec_product_id` | ulid FK nullable | → ec_products |
| `stripe_price_id` | string nullable | Stripe price object ID |
| `is_active` | boolean default true | |
| `sort_order` | integer default 0 | |

### `subscriptions`
| Column | Type | Notes |
|---|---|---|
| `subscription_plan_id` | ulid FK | → subscription_plans |
| `crm_contact_id` | ulid FK | → crm_contacts |
| `status` | enum | `trialing`, `active`, `past_due`, `cancelled`, `expired`, `paused` |
| `current_period_start` | timestamp | |
| `current_period_end` | timestamp | |
| `trial_ends_at` | timestamp nullable | |
| `cancelled_at` | timestamp nullable | |
| `cancellation_reason` | string nullable | |
| `paused_at` | timestamp nullable | |
| `resumes_at` | timestamp nullable | |
| `stripe_subscription_id` | string nullable | |
| `stripe_customer_id` | string nullable | |

### `subscription_invoices`
| Column | Type | Notes |
|---|---|---|
| `subscription_id` | ulid FK | → subscriptions |
| `amount` | decimal(10,2) | |
| `currency` | string(3) default 'GBP' | |
| `status` | enum | `draft`, `paid`, `failed`, `voided` |
| `billing_date` | date | |
| `paid_at` | timestamp nullable | |
| `stripe_invoice_id` | string nullable | |
| `invoice_pdf_url` | string nullable | Stripe-hosted PDF |

### `dunning_attempts`
| Column | Type | Notes |
|---|---|---|
| `subscription_id` | ulid FK | → subscriptions |
| `subscription_invoice_id` | ulid FK nullable | → subscription_invoices |
| `attempt_number` | integer | 1, 2, 3... |
| `attempted_at` | timestamp | |
| `status` | enum | `pending`, `success`, `failed` |
| `next_attempt_at` | timestamp nullable | |
| `failure_reason` | string nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `SubscriptionStarted` | `subscription_id`, `crm_contact_id` | [[Email Marketing]] (welcome sequence), CRM activity log |
| `SubscriptionRenewed` | `subscription_id` | Email receipt to customer |
| `SubscriptionCancelled` | `subscription_id`, `reason` | [[Email Marketing]] (win-back sequence), CRM activity |
| `PaymentFailed` | `subscription_id`, `attempt_number` | Dunning email to customer |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `CheckoutCompleted` | [[Storefront & Checkout]] | If checkout includes a subscription plan, create `subscription` record and initiate Stripe subscription |

---

## Permissions

```
ecommerce.subscription-plans.view
ecommerce.subscription-plans.create
ecommerce.subscription-plans.edit
ecommerce.subscription-plans.delete
ecommerce.subscriptions.view
ecommerce.subscriptions.create
ecommerce.subscriptions.edit
ecommerce.subscriptions.cancel
ecommerce.subscriptions.pause
ecommerce.subscription-invoices.view
ecommerce.dunning-attempts.view
```

---

## Related

- [[Ecommerce Overview]]
- [[Storefront & Checkout]]
- [[Subscription & MRR Tracking]]
- [[Email Marketing]]
- [[CRM — Contact & Company Management]]
