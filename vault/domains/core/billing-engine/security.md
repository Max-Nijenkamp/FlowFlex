---
domain: core
module: billing-engine
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Billing Engine — Security

Parent: [[_module]]

## Permissions

`core.billing.view-any` (billing list) · `core.billing.view` (single invoice) · `core.billing.activate-module` · `core.billing.deactivate-module` · `core.billing.manage-payment-method`

Owner-only by default for activate/deactivate *(assumed)*. Invoice state transitions (`open → paid → past_due → uncollectible`) are driven by Stripe webhooks and the dunning command under system context — they carry no user-facing permission. Seeded in `PermissionSeeder`.

## Rate Limiting

- `webhook` limiter on the Stripe webhook route (see below) — in addition to signature verification.
- `panel-action` limiter on the **manage payment method** header action — it calls the external Stripe API ([[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]).
- `exports`/PDF: `InvoiceMail` and invoice-PDF generation run on the notifications/finance queues as scheduled work, not interactive panel actions — no per-request limiter needed.

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
- **Rate limiter** (medium, from `_archive/build-history/security-audit-2026-06-11`): a dedicated `webhook` throttle limiter on the Stripe webhook route, in addition to signature verification.

> [!warning] UNVERIFIED
> The webhook route handler (`StripeWebhookController`) and `EnsureSubscriptionActive` middleware referenced here were not found in `app/`. See [[unknowns]].
