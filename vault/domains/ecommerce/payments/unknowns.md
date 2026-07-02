---
domain: ecommerce
module: payments
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payments — Unknowns

## Assumed Items

- `status` is a plain enum, no state-machine class *(assumed)*.
- Connected Stripe account per company *(assumed)* — pending the Connect-vs-keys ADR.

## Open Questions

> [!warning] UNVERIFIED
> Stripe Connect (platform collects, pays out) vs per-company Stripe keys is undecided — a **build-time ADR** is required. It affects platform fees, payout timing, and merchant onboarding.

- Which payment methods ship in v1 beyond card (iDEAL, SEPA confirmed EU-relevant; others?).
- Do failed-payment retries expire the order after N attempts / N days?
- Chargeback/dispute handling — surfaced in-app, or Stripe dashboard only for v1?

## Related

- [[../../../../build/decisions/decision-2026-06-01-stripe-cashier-vs-sdk]]
