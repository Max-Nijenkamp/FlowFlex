---
domain: core
module: billing-engine
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Billing Engine — Security

Parent: [[_module]]

## Permissions

`core.billing.view` · `core.billing.activate-module` · `core.billing.deactivate-module` · `core.billing.manage-payment-method`

Owner-only by default for activate/deactivate *(assumed)*.

## Authorization

Every Filament artifact gates on:
`canAccess() = Auth::user()->can('core.billing.view-any') && BillingService::hasModule('core.billing')`
per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. See [[../../../security/authn-authz]].

## Tenancy

Subscriptions and invoices are company-scoped via `CompanyScope`; company A's data is invisible to company B. See [[../../../security/tenancy-isolation]].

## PII / Encryption

`companies.stripe_customer_id` is stored encrypted (text column, `encrypted` cast). See [[../../../security/encryption]].

## Stripe webhook hardening

- Signature verification on the webhook (per [[../../../architecture/security]]).
- **Rate limiter** (medium, from `build/security-audit-2026-06-11`): a dedicated `webhook` throttle limiter on the Stripe webhook route, in addition to signature verification.

> [!warning] UNVERIFIED
> The webhook route handler (`StripeWebhookController`) and `EnsureSubscriptionActive` middleware referenced here were not found in `app/`. See [[unknowns]].
