---
type: module
domain: Core Platform
panel: app
module-key: core.billing
status: planned
color: "#4ADE80"
---

# Billing Engine

> Stripe-backed subscription system that controls which FlowFlex modules a company can access — per-module per-seat pricing, trial management, and webhook-driven status sync.

**Panel:** `app` (owner read-only plan view) + `admin` (full subscription management)
**Module key:** `core.billing`

## What It Does

The Billing Engine manages every company's subscription to FlowFlex. One `billing_subscriptions` row per company links to a Stripe customer and subscription. Access to every optional domain module is gated by an `EnforceModuleAccess` middleware that checks `company_module_subscriptions` before allowing any domain route. Stripe webhooks keep the local subscription status in sync with actual payment events. The admin panel shows MRR across all companies; the company owner sees their own plan and invoice history.

## Features

### Core
- One `billing_subscriptions` row per company with `status`: trialing / active / past_due / canceled / incomplete
- `company_module_subscriptions` table: one row per company per activated module, with `price_per_seat` and `seat_count`
- `EnforceModuleAccess` middleware: `middleware('module.access:hr')` — calls `BillingService::enforceModuleAccess()`, throws `ModuleAccessDeniedException` (renders 402 upgrade page) if module inactive
- Foundation modules whitelisted and always pass enforcement: `['company', 'users', 'audit', 'notifications', 'setup']`
- `BillingService::ensureStripeCustomer()` — creates Stripe customer on first billing action, persists `stripe_customer_id`

### Advanced
- Stripe webhook handler for: `invoice.payment_succeeded` (mark active), `invoice.payment_failed` (mark past_due, notify owner), `customer.subscription.updated` (sync status and period dates), `customer.subscription.deleted` (mark canceled)
- `billing_invoices` table stores local copy of each Stripe invoice for display without Stripe API calls
- `BillingService::calculateMonthlyAmount()` — sums `price_per_seat × seat_count` across all active modules
- Trial support: `trial_ends_at` on billing subscription; trialing companies pass enforcement until trial expires
- Stripe client lazy-initialized with `STRIPE_SECRET` guard — throws `RuntimeException` if not configured (prevents CI failures)

### AI-Powered
- Churn prediction: companies approaching trial expiry with low usage scores flagged in admin panel for proactive outreach
- Upgrade recommendations: usage patterns (near module limits, high user count) surfaced as upgrade prompts to company owner

## Data Model

```erDiagram
    billing_subscriptions {
        ulid id PK
        ulid company_id FK "unique"
        string plan
        string status
        decimal monthly_amount
        string stripe_customer_id
        string stripe_subscription_id
        timestamp trial_ends_at
        timestamp current_period_start
        timestamp current_period_end
        timestamp ends_at
        timestamps created_at/updated_at
    }

    company_module_subscriptions {
        ulid id PK
        ulid company_id FK
        string module_key
        boolean is_active
        decimal price_per_seat
        integer seat_count
        timestamp activated_at
        timestamps created_at/updated_at
    }

    billing_invoices {
        ulid id PK
        ulid company_id FK
        string stripe_invoice_id "unique"
        decimal amount
        string currency
        string status
        timestamp paid_at
        string invoice_url
        timestamps created_at/updated_at
    }
```

| Table | Purpose |
|---|---|
| `billing_subscriptions` | One per company — Stripe link + status |
| `company_module_subscriptions` | Per-module access records |
| `billing_invoices` | Local copy of Stripe invoices |

## Permissions

- `core.billing.view`
- `core.billing.manage`
- `core.billing.view-invoices`
- `core.billing.activate-module`
- `core.billing.deactivate-module`

## Filament

- **Resource:** `BillingResource` (admin panel — read-only list of all company subscriptions + MRR widget)
- **Pages:** `BillingOverviewPage` (app panel — current plan, active modules, invoice history)
- **Custom pages:** `BillingOverviewPage`
- **Widgets:** `MrrWidget` (admin panel), `PlanSummaryWidget` (app panel)
- **Nav group:** Billing (app panel); Billing (admin panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Chargebee | Subscription billing management |
| Recurly | Per-module SaaS billing |
| Paddle | Stripe-based SaaS billing |
| Zuora | Enterprise subscription management |

## Related

- [[module-marketplace]]
- [[setup-wizard]]
- [[notifications]]
- [[audit-log]]
