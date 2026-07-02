---
domain: ecommerce
module: payments
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payments — Decisions

## ADR: Raw Stripe SDK, not Cashier

- **Decision:** Use `stripe/stripe-php` directly (not Laravel Cashier) for e-commerce order payments. See [[../../../../build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk]].
- **Consequences:** Full control over Payment Intents, iDEAL/SEPA methods, and idempotency; more wiring than Cashier.

## ADR: Payments drives orders via `OrderService` (no direct writes)

- **Decision:** On `payment_intent.succeeded`, the webhook records the payment row then calls `OrderService::markPaid`. Payments never writes `ec_orders`.
- **Consequences:** Order state changes stay owned by Orders; the bounded-context write boundary holds ([[../../../../security/data-ownership]]).

## ADR: No local card storage — Stripe references only

- **Decision:** Store only Stripe intent/customer ids; saved methods live as Stripe customer objects.
- **Consequences:** PCI scope minimised; nothing sensitive to encrypt locally.

## Open ADR: Stripe Connect vs per-company keys

- **Context:** Platform-collected (Connect) vs each company's own Stripe keys — affects fees + onboarding.
- **Status:** **Build-time ADR required** *(assumed connected-account per company for now)*.
