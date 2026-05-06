---
tags: [flowflex, core, billing, stripe, modules, phase/1]
domain: Core Platform
panel: workspace
color: "#2199C8"
status: built
last_updated: 2026-05-06
---

# Module Billing Engine

The commercial engine of FlowFlex. Tracks which modules each tenant has active, meters usage, and bills via Stripe.

**Who uses it:** Workspace owners/admins, FlowFlex billing system
**Filament Panel:** `workspace`
**Depends on:** [[Authentication & Identity]], [[Multi-Tenancy & Workspace]]
**Build complexity:** High — 1 resource, 3 pages, 4 tables

## Events Fired

- `ModuleActivated` — tenant turned on a module
- `ModuleDeactivated` — tenant turned off a module
- `SubscriptionUpgraded` — plan tier changed
- `SubscriptionDowngraded` — plan tier reduced
- `PaymentFailed` — triggers grace period and notifications
- `TrialExpired` — tenant trial ended without converting

## Module Toggle

- Module marketplace — grid of all available modules with descriptions and pricing
- Toggle modules on/off per tenant (data is **never** deleted on deactivation, only hidden)
- Module dependencies shown ("Finance requires Core Invoicing")
- Preview mode — explore a module's UI without activating it (read-only demo data)
- Module activation wizard (guided setup for complex modules like Payroll)

## Plan Tiers

### Starter

- Up to 10 users
- Up to 5 modules active
- 5GB file storage
- Standard support

### Pro

- Up to 100 users
- Unlimited modules
- 100GB file storage
- Priority support
- API access

### Enterprise

- Unlimited users
- Unlimited modules
- Unlimited file storage
- Custom SLA
- Dedicated account manager
- SSO + SCIM provisioning
- Custom contracts

## Plan Management Features

- Plan comparison and upgrade flow (self-serve)
- Stripe Billing integration (subscriptions, proration, invoices)
- Annual discount toggle (2 months free on annual billing)
- Seat-based and module-based pricing combined
- Overage handling (soft limits with notifications)

## Usage Metering

- Event-based metering (each module emits usage events to `module_usage_events` table)
- Usage dashboard per tenant (see what you're consuming)
- Billing period usage summary
- Alerts when approaching plan limits (at 80% and 100%)
- Historical usage graphs
- Stripe usage records synced from `module_usage_events` via scheduled job

## Trial Management

- 14-day free trial (configurable per module)
- Trial includes all Pro features
- Trial countdown banner in the workspace
- Conversion flow at trial expiry
- FlowFlex sales team notification when high-value trial starts

## Database Tables (4)

1. `subscriptions` — Stripe subscription records per tenant
2. `tenant_modules` — which modules are active per tenant, with activation timestamps
3. `module_usage_events` — metered events per tenant per module
4. `billing_invoices` — invoice history from Stripe

## Related

- [[Multi-Tenancy & Workspace]]
- [[Multi-Tenancy]]
- [[Authentication & Identity]]
- [[Workspace Panel]]
- [[Build Order (Phases)]]
