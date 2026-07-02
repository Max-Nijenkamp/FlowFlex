---
domain: ecommerce
module: abandoned-cart
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Abandoned Cart — Unknowns

## Assumed Items

- Recovery schedule 1h/24h/72h with a discount on the 3rd, steps configurable *(assumed)*.
- Carts purged at 90 days *(assumed)*.
- Own opt-out link for suppression in v1 *(assumed)*; central marketing suppression list integration deferred.
- `CartAbandoned` cross-domain event dropped *(assumed)*.

## Open Questions

- SMS/WhatsApp recovery channel (not just email) — which release? (see [[../_opportunities|opportunities]].)
- Should recovery attribution feed analytics/finance (revenue-recovered reporting) via an event, or stay in-module?
- Does the incentive coupon require promotions active, or a built-in fallback discount when promotions is off?
- Suppression: integrate with a central `marketing.campaigns` opt-out list when that domain lands?
