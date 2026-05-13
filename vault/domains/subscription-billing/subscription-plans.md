---
type: module
domain: Subscription Billing & RevOps
panel: billing
module-key: billing.plans
status: planned
color: "#4ADE80"
---

# Subscription Plans

> Subscription plan catalog — pricing tiers, included features, trial periods, and per-seat or flat-rate billing configurations.

**Panel:** `billing`
**Module key:** `billing.plans`

---

## What It Does

Subscription Plans is the catalog of all pricing tiers the company offers. It defines how each plan is priced (flat monthly/annual rate, per-seat, usage-based, or hybrid), which features or modules are included, and the trial period configuration. Plan records are referenced by Stripe products/prices, ensuring billing stays synchronised. When a customer moves between plans, the upgrade or downgrade is recorded against their subscription with the effective date and proration handling.

---

## Features

### Core
- Plan creation: name, description, billing interval (monthly, annual, quarterly), pricing model
- Pricing models: flat rate, per-seat (per user), tiered per seat, usage-based, or hybrid
- Feature inclusion: list of features or FlowFlex modules included in each plan
- Trial period: number of trial days and which plan the trial converts to
- Stripe synchronisation: plans synced to Stripe Products and Prices automatically
- Plan status: active, archived (grandfathered), or draft

### Advanced
- Annual vs monthly pricing: configure a discount for annual payment vs monthly
- Currency variants: configure prices in multiple currencies for the same plan
- Add-ons: optional paid add-ons that can be attached to any base plan
- Volume discounts: configurable volume discount schedule for seat-based plans
- Grandfathering: archive a plan while keeping existing subscribers on it

### AI-Powered
- Price optimisation suggestion: analyse conversion rates and churn by plan to suggest pricing adjustments
- Feature usage correlation: identify which included features correlate with retention to inform plan design
- Plan comparison generator: AI drafts a marketing comparison table from plan feature lists

---

## Data Model

```erDiagram
    subscription_plans {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string pricing_model
        decimal base_price
        string currency
        string billing_interval
        integer trial_days
        json included_features
        string stripe_product_id
        string stripe_price_id
        string status
        timestamps created_at_updated_at
    }

    plan_add_ons {
        ulid id PK
        ulid plan_id FK
        string name
        decimal price
        string stripe_price_id
        timestamps created_at_updated_at
    }

    subscription_plans ||--o{ plan_add_ons : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `subscription_plans` | Plan definitions | `id`, `company_id`, `name`, `pricing_model`, `base_price`, `billing_interval`, `stripe_product_id`, `status` |
| `plan_add_ons` | Optional add-ons | `id`, `plan_id`, `name`, `price`, `stripe_price_id` |

---

## Permissions

```
billing.plans.view
billing.plans.create
billing.plans.update
billing.plans.delete
billing.plans.archive
```

---

## Filament

- **Resource:** `App\Filament\Billing\Resources\SubscriptionPlanResource`
- **Pages:** `ListSubscriptionPlans`, `CreateSubscriptionPlan`, `EditSubscriptionPlan`, `ViewSubscriptionPlan`
- **Custom pages:** `PlanComparisonPage`, `StripeSyncStatusPage`
- **Widgets:** `ActivePlansWidget`, `SubscribersByPlanWidget`
- **Nav group:** Subscriptions

---

## Displaces

| Feature | FlowFlex | Chargebee | Zuora | Paddle |
|---|---|---|---|---|
| Flexible pricing models | Yes | Yes | Yes | Yes |
| Stripe synchronisation | Yes | Yes | Partial | Yes |
| Add-on configuration | Yes | Yes | Yes | Yes |
| AI price optimisation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[invoicing]] — invoices generated from plan price and billing interval
- [[dunning]] — dunning settings linked to plan tier
- [[revenue-recognition]] — plan billing interval drives revenue recognition schedule
- [[mrr-analytics]] — plan movements feed MRR metrics
