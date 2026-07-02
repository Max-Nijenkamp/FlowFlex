---
domain: core
module: billing-engine
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Billing Engine

Manages company subscriptions to FlowFlex modules: activation/deactivation, monthly invoice calculation, Stripe payment processing, dunning for failed payments, and MRR/churn metrics. The central gating service for all optional domain modules — `BillingService::hasModule()` is called by every `canAccess()` in the product.

- **module-key:** `core.billing` · **panel:** app + admin · **priority:** v1-core
- **fires-events:** `ModuleActivated`, `CompanySubscriptionSuspended`

## Sibling notes

- [[architecture]] — services, actions, states, jobs, commands + flow diagram
- [[data-model]] — tables, columns, ERD
- [[api]] — events, DTOs, `BillingServiceInterface`
- [[security]] — permissions, tenancy, encrypted `stripe_customer_id`
- [[decisions]] — Stripe SDK (not Cashier)
- [[unknowns]] — UNVERIFIED Build-Manifest items
- Features: [[features/monthly-invoicing]] · [[features/dunning]] · [[features/stripe-integration]] · [[features/module-gating]] · [[features/admin-metrics]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.panels | `/app` + `/admin` surfaces |
| Hard | foundation.tenancy | subscriptions company-scoped |
| Hard | foundation.queues | invoice generation + dunning jobs |
| Hard | core.settings | currency, company identity on invoices |
| Soft | core.notifications | consumes ModuleActivated/Suspended; without it events fire unconsumed |

Consumed by [[../module-marketplace/_module]] (UI over this service) and [[../staff-console/_module]] (BillingService + invoice revenue data).

## Core Features

- `BillingService::hasModule(string $key)` — single method all `canAccess()` checks call (cached 5 min, see [[../../../architecture/caching]])
- Module activation: one-click from marketplace, recorded in `company_module_subscriptions`
- Module deactivation: gates access, retains data; reactivation creates a new row (history preserved)
- Monthly invoice calculation: `sum(module_price_per_user) × active_user_count`
- Stripe integration: raw `stripe/stripe-php` SDK — see [[decisions]]
- Dunning: payment retry schedule (3 attempts over 14 days *(assumed)*), suspension after final failure
- Subscription status: `trial → active → suspended → cancelled` on `companies.subscription_status`
- MRR tracking, churn metrics, module adoption rates (surfaced in `/admin`)
- Recurring invoice PDF generation and email delivery

## Test Checklist

- [ ] Tenant isolation: company A subscriptions invisible to company B
- [ ] `hasModule` true after activation, false after deactivation, within one request (cache bust)
- [ ] Free core modules cannot be deactivated
- [ ] Invoice calculation: 15 users × 3 modules matches pricing-model to the cent (brick/money)
- [ ] `GenerateMonthlyInvoicesCommand` idempotent (run twice = one invoice)
- [ ] Stripe webhook with bad signature → 400, no state change
- [ ] `invoice.payment_succeeded` webhook → status paid + `paid_at`
- [ ] Dunning exhaustion fires `CompanySubscriptionSuspended` + company suspended
- [ ] Suspended company blocked from panels by middleware

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_company_module_subscriptions_table.php
database/migrations/xxxx_create_billing_invoices_table.php
database/migrations/xxxx_create_billing_invoice_lines_table.php
app/Models/{ModuleCatalog(sushi),CompanyModuleSubscription,BillingInvoice,BillingInvoiceLine}.php
app/States/BillingInvoice/{BillingInvoiceState,Draft,Open,Paid,PastDue,Uncollectible}.php
app/Data/{ActivateModuleData,BillingInvoiceData}.php
app/Contracts/BillingServiceInterface.php
app/Services/BillingService.php
app/Events/{ModuleActivated,CompanySubscriptionSuspended}.php
app/Listeners/{NotifyModuleActivatedListener,NotifySubscriptionSuspendedListener}.php
app/Notifications/{ModuleActivatedNotification,SubscriptionSuspendedNotification}.php
app/Console/Commands/{GenerateMonthlyInvoicesCommand,ProcessDunningCommand}.php
app/Mail/InvoiceMail.php
app/Filament/App/Resources/BillingResource.php (+ Pages/ListBillingInvoices)
```

> [!warning] UNVERIFIED Build-Manifest items
> The original spec listed `app/Filament/App/Widgets/BillingWidget.php`, `app/Http/Controllers/Webhooks/StripeWebhookController.php`, `app/Http/Middleware/EnsureSubscriptionActive.php`, `app/Exceptions/Core/{ModuleAlreadyActiveException,CannotDeactivateCoreModuleException}.php`, and `BillingOverviewResource`/`ModulePricingResource`. These were **not found** in `app/`. See [[unknowns]].

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | `ModuleActivated` | core.notifications | listener notifies owner/admins a module was activated |
| fires | `ModuleActivated` | core.rbac | RBAC scopes assignable permissions to the now-active module (read) |
| fires | `CompanySubscriptionSuspended` | core.notifications | listener mails the owner (must not require panel access) |
| consumes | none | — | Stripe webhook signals are external, not domain events |

Data ownership: billing-engine owns and writes only `company_module_subscriptions`, `billing_invoices`, `billing_invoice_lines`, `module_catalog` (Sushi), and the `companies.stripe_customer_id` / `companies.subscription_status` columns it manages; it reads active-user count and currency read-only via tenancy/`core.settings`, and effects other domains only via events ([[../../../security/data-ownership]]).

## Related

- [[../module-marketplace/_module]] · [[../staff-console/_module]]
- [[../../../architecture/module-system]] · [[../../../architecture/event-bus]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]] · [[../../../security/data-ownership]]
- [[../../../glossary]]
