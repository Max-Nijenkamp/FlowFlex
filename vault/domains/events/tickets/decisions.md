---
domain: events
module: tickets
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tickets — Decisions

## ADR: Stripe raw SDK, not Cashier

- **Context:** Paid tickets need per-purchase PaymentIntents, not subscriptions.
- **Decision:** Use the raw `stripe/stripe-php` SDK with idempotency keys (consistent with the platform Stripe ADR: [[../../../decisions/decision-2026-06-01-stripe-cashier-vs-sdk]]). Connect-vs-keys follows the shared ecommerce decision *(assumed)*.
- **Consequences:** Full control over the payment lifecycle; webhook-driven confirmation.

## ADR: Atomic sold-count prevents oversell

- **Context:** Concurrent purchases at the quantity limit could oversell.
- **Decision:** `quantity_sold` is incremented atomically (conditional update) inside the purchase transaction, guarding on `quantity_available`.
- **Consequences:** No oversell under concurrency; sold-out is deterministic.

## ADR: Purchase confirms registration via service call

- **Context:** A paid registration should confirm only after payment.
- **Decision:** On webhook success, Tickets calls `RegistrationService::confirm` (same-domain service), rather than writing `ev_registrations` directly. Refund calls `cancel`.
- **Consequences:** Bounded-context integrity; registrations owns its own state. See [[../../../security/data-ownership]].

## ADR: Finance revenue link is manual in v1 *(assumed)*

- **Context:** Ticket revenue should eventually post to Finance.
- **Decision (assumed):** v1 uses a manual "create invoice" bridge action rather than an automatic GL posting; Finance owns `fin_*` tables.
- **Consequences:** Loose coupling; revocable when a Finance integration is designed. See [[unknowns]].
