---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Raw Stripe SDK vs Laravel Cashier

---

## Context

FlowFlex needs Stripe integration for billing. Two options:

1. **`laravel/cashier`** — Laravel's first-party Stripe package. Provides a Stripe customer per user, subscription management, invoice generation, and webhook handling via a clean Laravel API.

2. **`stripe/stripe-php`** — Raw Stripe SDK. Full access to all Stripe APIs. No Laravel integration layer.

---

## Decision

**Use `stripe/stripe-php` directly. Skip `laravel/cashier`.**

---

## Rationale

Cashier's subscription model is: one customer → one subscription → one or more prices. This maps well to: "company pays €49/month for the Pro plan."

FlowFlex's billing model is: one company → many subscription items (one per activated module) × active user count. The monthly total changes dynamically when modules are activated/deactivated or user count changes.

Cashier's assumptions break down:
- Cashier expects subscription items to be added at subscription creation, not dynamically throughout the month
- Cashier's `billable` is typically a `User` model. FlowFlex bills a `Company` — multiple users, one bill.
- Cashier's invoice model assumes fixed periods. FlowFlex needs proration when modules change mid-month.
- Cashier doesn't support the "total = Σ(module_price × user_count)" calculation pattern.

The custom `BillingService` using raw Stripe SDK:
- Creates one `Customer` per company (on company creation)
- Creates one `SubscriptionItem` per active module (priced as `unit_amount × quantity = user_count`)
- Updates `SubscriptionItem` quantity when user count changes
- Adds/removes `SubscriptionItem` when modules are activated/deactivated
- Uses Stripe's subscription-level `proration_behavior: 'create_prorations'` for mid-cycle changes
- Receives `invoice.payment_succeeded` and `customer.subscription.deleted` webhooks

---

## Consequences

- More Stripe API code to write and maintain vs Cashier
- No Cashier helper methods (`.charge()`, `.subscribedToProduct()`, etc.) — must implement equivalents
- Full control over billing logic — no Cashier conventions to work around
- Can implement complex proration, volume discounts, and trial period logic without Cashier limitations
- Stripe webhook handling is custom — must verify signatures manually (documented in [[architecture/security]])

---

## Related

- [[domains/core/billing-engine]]
- [[product/pricing-model]]
- [[architecture/security]] — Stripe webhook signature verification
