---
domain: ecommerce
module: abandoned-cart
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Abandoned Cart — Decisions

## ADR: Recovery is same-domain — `CartAbandoned` event dropped

- **Decision:** The v1 spec's cross-domain `CartAbandoned` event is not fired. Detection is an in-domain scheduled job; recovery mails are sent from this module.
- **Consequences:** No cross-domain contract for abandonment; simpler blast radius. (Mirrors the Orders ADR.)

## ADR: Scheduled detection with idempotent steps

- **Decision:** `ProcessAbandonedCartsCommand` runs every 15 min; each recovery step is a unique `(cart_id, step)` row so a step fires at most once; status guards stop the sequence on `converted`/`recovered`.
- **Consequences:** No duplicate mails under overlapping runs; safe retries.

## ADR: Signed single-use restore token

- **Decision:** The recovery link is a Laravel signed URL validating `recovery_token`, treated as a single-use capability on the guest guard, rate-limited.
- **Consequences:** No auth needed to resume a cart; token can't be replayed. Security-audit HIGH item addressed.

## ADR: 90-day purge (assumed)

- **Decision:** `PruneCartsCommand` deletes carts older than 90 days *(assumed)*.
- **Consequences:** GDPR-aligned retention; window configurable later.
