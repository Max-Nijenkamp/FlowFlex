---
domain: core
module: billing-engine
feature: stripe-integration
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Stripe Integration

Parent: [[../_module]] · See [[../decisions]] · [[../security]]

Raw `stripe/stripe-php` SDK (not Cashier — [[../decisions]]).

- Customer creation; `companies.stripe_customer_id` stored encrypted.
- Subscription item per active module on activate; removed on deactivate.
- Invoice generation: FlowFlex creates Stripe invoices directly.
- Webhook handling: `BillingService::handleStripeWebhook($event)` routes per event type. `invoice.payment_succeeded` → `paid` + `paid_at`; payment failed → `past_due`.
- Signature verification + dedicated `webhook` rate limiter on the route ([[../security]]).

> [!warning] UNVERIFIED
> `StripeWebhookController` (the route handler invoking `handleStripeWebhook`) was not found in `app/`. The service method itself is part of `BillingService`. See [[../unknowns]].

## UI

- **Kind**: background (webhook + sync) + simple-resource (payment method)
- **Page**: webhook handling is background (no page) — a signed Stripe route → `BillingService::handleStripeWebhook`. The customer-facing surface is the payment-method section of `BillingResource` at `/app/billing`.
- **Layout**: payment-method panel — card brand/last-4, "manage payment method" action (Stripe-hosted or Elements). Subscription-item sync is invisible (runs inside activate/deactivate).
- **Key interactions**: an owner opens billing → adds/updates the payment method; webhook events (payment succeeded/failed) are unattended and mutate invoice state.
- **States**: empty = no payment method on file (prompt to add) · loading = Stripe form/redirect pending · error = declined card / bad webhook signature → 400, no state change · selected = active card shown with last-4.
- **Gating**: `core.billing.manage-payment-method` for the card surface; webhook route is unauthenticated but signature-verified + `webhook` rate-limited.

## Data

- Owns / writes: `billing_invoices` (webhook routes `invoice.payment_succeeded` → `paid` + `paid_at`; `payment_failed` → `past_due`), `companies.stripe_customer_id` (encrypted). Subscription items are Stripe-side, mirrored from `company_module_subscriptions`.
- Reads: Stripe API (external, read/write to Stripe — not a FlowFlex domain). No other FlowFlex domain's tables.
- Cross-domain writes: none — see [[../../../../security/data-ownership]].

## Relations

- Consumes: Stripe webhook events (external system, not a domain event) → drives invoice state.
- Feeds: `payment_failed` starts dunning ([[dunning]]); no cross-domain event emitted here.
- Shared entity: `companies.stripe_customer_id` — owned/written by this module only.

> [!warning] UNVERIFIED
> Webhook route handler (`StripeWebhookController`) and the `webhook` rate limiter binding were not found in `app/`. See [[../unknowns]].
