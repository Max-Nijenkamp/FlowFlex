---
tags: [flowflex, domain/marketing, affiliate, partners, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# Affiliate & Partner Management

Run a branded affiliate programme with automated tracking, commission calculation, and payout processing. Reward partners for every sale they drive without manual bookkeeping.

**Who uses it:** Marketing team, partnership managers, finance (for payouts), affiliates (via portal)
**Filament Panel:** `marketing`
**Depends on:** [[CRM — Contact & Company Management]], [[Order Management]], Finance
**Phase:** 5
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **Affiliate onboarding** — create affiliates from existing CRM contacts; generate a unique referral code; set commission structure
- **Referral link generation** — each affiliate gets a unique trackable URL (e.g. `yoursite.com/?ref=JOHN2024`); UTM parameters auto-appended
- **Commission types** — percentage of order value, flat rate per order, or tiered by monthly volume
- **Referral tracking** — when an order is placed with a referral code, a `affiliate_referrals` record is created with commission amount calculated
- **Commission approval workflow** — referrals start as `pending`; manager reviews and approves before commission is added to payout queue
- **Payout management** — batch approved commissions into a payout per affiliate per period; mark as paid with payment reference
- **Payout threshold** — affiliates only get paid when earned commission exceeds their `payout_threshold`
- **Payment method flexibility** — affiliates specify preferred payment method (bank transfer, PayPal, Wise) stored as JSON
- **Affiliate performance dashboard** — clicks, conversions, total commission earned, top affiliates ranked by revenue
- **Affiliate portal (external)** — branded read-only portal where affiliates log in to see their referrals, earnings, and payout history
- **`AffiliateCommissionEarned` event** — fires on each approved referral; notifies affiliate via email
- **`AffiliatePayoutProcessed` event** — fires when a payout is marked paid; sends payment confirmation to affiliate
- **Fraud detection flags** — flag referrals where the affiliate and buyer email match, or same IP is used repeatedly

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `affiliates`
| Column | Type | Notes |
|---|---|---|
| `crm_contact_id` | ulid FK | → crm_contacts |
| `code` | string unique per company | referral code |
| `commission_rate` | decimal(8,4) | percentage or flat amount |
| `commission_type` | enum | `percentage`, `flat` |
| `status` | enum | `active`, `paused`, `terminated` |
| `total_earned` | decimal(10,2) default 0 | lifetime earnings |
| `total_paid` | decimal(10,2) default 0 | |
| `payout_threshold` | decimal(10,2) default 0 | minimum balance before payout |
| `payment_method` | json nullable | {type: "paypal", email: "..."} |
| `portal_password_hash` | string nullable | hashed cast — for affiliate portal login |
| `notes` | text nullable | |

### `affiliate_referrals`
| Column | Type | Notes |
|---|---|---|
| `affiliate_id` | ulid FK | → affiliates |
| `order_id` | ulid FK nullable | → orders |
| `crm_contact_id` | ulid FK nullable | → crm_contacts (the buyer) |
| `commission_amount` | decimal(10,2) | |
| `order_value` | decimal(10,2) | |
| `status` | enum | `pending`, `approved`, `rejected`, `paid` |
| `referred_at` | timestamp | |
| `approved_at` | timestamp nullable | |
| `approved_by` | ulid FK nullable | → tenants |
| `rejection_reason` | string nullable | |
| `is_flagged` | boolean default false | fraud flag |

### `affiliate_payouts`
| Column | Type | Notes |
|---|---|---|
| `affiliate_id` | ulid FK | → affiliates |
| `amount` | decimal(10,2) | |
| `currency` | string(3) default 'GBP' | |
| `period_start` | date | |
| `period_end` | date | |
| `paid_at` | timestamp nullable | |
| `payment_reference` | string nullable | bank ref or PayPal transaction ID |
| `status` | enum | `pending`, `processing`, `paid`, `failed` |
| `notes` | text nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `AffiliateCommissionEarned` | `affiliate_referral_id`, `affiliate_id` | Email notification to affiliate |
| `AffiliatePayoutProcessed` | `affiliate_payout_id`, `affiliate_id` | Email payment confirmation to affiliate |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `OrderCompleted` | [[Order Management]] | Check if order has referral code; if so, create `affiliate_referrals` record and calculate commission |

---

## Permissions

```
marketing.affiliates.view
marketing.affiliates.create
marketing.affiliates.edit
marketing.affiliates.delete
marketing.affiliate-referrals.view
marketing.affiliate-referrals.approve
marketing.affiliate-referrals.reject
marketing.affiliate-payouts.view
marketing.affiliate-payouts.create
marketing.affiliate-payouts.process
```

---

## Related

- [[Marketing Overview]]
- [[Order Management]]
- [[Invoicing]]
- [[CRM — Contact & Company Management]]
