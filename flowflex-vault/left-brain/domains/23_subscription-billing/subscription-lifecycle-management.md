---
type: module
domain: Subscription Billing & RevOps
panel: subscriptions
phase: 3
status: complete
cssclasses: domain-subscriptions
migration_range: 975000–975299
last_updated: 2026-05-12
---

# Subscription Lifecycle Management

Manage the full lifecycle of customer subscriptions: plan creation, subscription activation, upgrades, downgrades, pauses, cancellations, and trial-to-paid conversion.

---

## Plan Catalogue

Plans define what customers can subscribe to:
- **Plan name** and public description
- **Billing interval**: monthly / quarterly / annual / multi-year
- **Price**: flat rate, per-seat, per-usage, tiered, volume, graduated
- **Trial period**: days of free trial (0 = no trial)
- **Features included**: list of modules/features unlocked (maps to FlowFlex feature flags or custom entitlements)
- **Add-ons**: optional extras purchasable alongside plan

### Pricing Models

| Model | Example |
|---|---|
| Flat rate | €299/month regardless of seats |
| Per seat | €29/user/month |
| Tiered | 1–10 seats: €29/seat; 11–50: €22/seat |
| Volume | If total seats ≥ 11, all seats at €22 |
| Graduated | First 10 at €29, next 40 at €22, next 50 at €15 |
| Usage-based | €0.01 per API call (handled in [[usage-based-billing]]) |

---

## Subscription States

```
Trial → Active → (Paused) → Cancelled
                ↓
           Past Due (payment failed → dunning)
                ↓
        Cancelled (dunning exhausted)
```

### State Transitions
- Trial → Active: automatic on trial end (if payment method on file) or manual upgrade
- Active → Paused: customer-initiated, subscription suspended for N days, resumes automatically
- Active → Cancelled: immediate (refund period) or end of billing period
- Upgrade: new plan effective immediately + proration
- Downgrade: effective at end of current billing period

---

## Proration

When customer upgrades mid-cycle:
```
Unused days of current plan = credit
Remaining days of new plan = charge
Net difference charged/credited immediately
```

Proration calculation exact to the day. Optionally: round to nearest billing period.

---

## Data Model

### `sub_plans`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| billing_interval | enum | monthly/quarterly/annual/custom |
| interval_count | int | e.g., 2 = every 2 months |
| pricing_model | enum | flat/per_seat/tiered/volume/graduated |
| base_price | decimal(14,4) | |
| currency | char(3) | |
| trial_days | int | default 0 |
| active | bool | |

### `sub_subscriptions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| crm_company_id | ulid | FK |
| plan_id | ulid | FK |
| status | enum | trial/active/paused/past_due/cancelled |
| quantity | int | seat count or 1 for flat |
| current_period_start | date | |
| current_period_end | date | |
| trial_ends_at | date | nullable |
| cancelled_at | timestamp | nullable |
| cancellation_reason | varchar(200) | nullable |

---

## Migration

```
975000_create_sub_plans_table
975001_create_sub_plan_tiers_table
975002_create_sub_subscriptions_table
975003_create_sub_subscription_items_table
975004_create_sub_add_ons_table
```

---

## Related

- [[MOC_SubscriptionBilling]]
- [[recurring-billing-engine]]
- [[dunning-management]]
- [[mrr-arr-analytics]]
- [[MOC_CRM]] — subscription linked to company
