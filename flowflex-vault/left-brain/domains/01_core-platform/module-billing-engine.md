---
type: module
domain: Core Platform
panel: admin + app
cssclasses: domain-admin
phase: 1
status: in-progress
migration_range: 010001–019999
last_updated: 2026-05-10
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# Module Billing Engine

Stripe-backed subscription system that controls which FlowFlex modules a company can access. Each module is priced per user per month. Foundation modules (company, users, audit, notifications, setup wizard) are always free. All other modules require an active subscription entry.

**Panel:** `admin` (subscription management + MRR dashboard) + `app` (read-only plan view for company owner)  
**Phase:** 1 — access enforcement must exist before Phase 2 domain modules are activated

---

## Features

### Subscription Management
- One `billing_subscriptions` row per company (unique `company_id`)
- Plan field describes the active tier; `monthly_amount` is calculated from active modules × seat count
- `trial_ends_at` supports a free trial period before payment is required
- Status values: `trialing`, `active`, `past_due`, `canceled`, `incomplete`

### Module Access Enforcement
- `EnforceModuleAccess` middleware: `middleware('module.access:hr')` gates any route to a specific domain module
- Calls `BillingService::enforceModuleAccess(company, module)` — throws `ModuleAccessDeniedException` (renders a 402 upgrade page) if inactive
- Foundation modules are whitelisted and always pass — `['company', 'users', 'audit', 'notifications', 'setup']`

### BillingService
- `calculateMonthlyAmount(Company $company): float` — sums per-module per-seat pricing
- `ensureStripeCustomer(Company $company): string` — creates Stripe customer if not exists, persists `stripe_customer_id`; uses `withoutGlobalScopes()` since company is passed explicitly
- `isBillingActive(Company $company): bool` — returns `true` if status is `active` or `trialing` and not expired; uses `withoutGlobalScopes()`
- `enforceModuleAccess(Company $company, string $module): void` — throws on inactive access
- Stripe client is lazy-initialised: `private ?StripeClient $stripe = null` with accessor that throws `RuntimeException` if `STRIPE_SECRET` is not configured — prevents test suite failures in CI

### Stripe Webhooks
Handled events:
- `invoice.payment_succeeded` — marks subscription `active`, updates `current_period_start/end`
- `invoice.payment_failed` — marks subscription `past_due`, triggers owner notification
- `customer.subscription.updated` — syncs plan, status, period dates
- `customer.subscription.deleted` — marks subscription `canceled`, sets `ends_at`

Webhook endpoint excluded from CSRF and authentication middleware.

### Invoices
- `billing_invoices` stores a local copy of each Stripe invoice for display in the admin panel
- Fields: Stripe invoice ID, amount, currency, status, paid_at, invoice_url (Stripe-hosted PDF)

### Admin Panel
- `BillingResource` in admin panel: read-only list of all company subscriptions
- MRR widget: sum of `monthly_amount` where `status IN ('active', 'trialing')`
- No create/edit from admin — all mutations go through Stripe

---

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

    billing_subscriptions ||--o{ billing_invoices : "company_id"
```

---

## Permissions

```
core.billing.view
core.billing.manage
```

---

## Related

- [[MOC_CorePlatform]]
- [[entity-module-subscription]]
- [[setup-wizard]] — Step 4 links to module selection
- [[concept-multi-tenancy]]
