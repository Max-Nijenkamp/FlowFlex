---
domain: ecommerce
module: promotions
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Promotions — Unknowns

## Assumed Items

- v1 rule set = threshold (free shipping over X) + product/category conditions; buy-X-get-Y deferred *(assumed)*.
- Stacking: one coupon per order, auto-promotions stack *(assumed)*.

## Open Questions

- BXGY (buy-X-get-Y) and tiered discounts — which release?
- Segment-conditioned promotions depend on `crm.customer-segments`; behaviour when CRM is inactive (skip segment rules?).
- Should coupon codes be auto-generatable in bulk (campaign codes), or manual only in v1?
- Do auto-promotions need scheduling windows finer than date (e.g. flash-sale hours)?
