---
domain: ecommerce
module: promotions
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Promotions — Decisions

## ADR: One `DiscountEngine` API for all discounting

- **Decision:** Orders/checkout call a single `DiscountEngine::apply(cart, code)`; it validates the coupon and layers auto-promotions, returning a `DiscountResult`. Redemption (`redeem`) is a separate atomic step at order-paid.
- **Consequences:** Callers never re-implement discount logic; validation is centralised + testable.

## ADR: Percent in basis points, fixed in cents

- **Decision:** `discount_value` is basis points for percent (≤ 10000) and cents for fixed; math via `brick/money`.
- **Consequences:** No float rounding; precise percentage discounts.

## ADR: Stacking — one coupon per order, auto-promotions stack (assumed)

- **Decision:** A cart may carry at most one coupon; automatic promotions stack with it *(assumed)*.
- **Consequences:** Predictable discount totals; revisit for multi-coupon campaigns later.

## ADR: Atomic usage counters

- **Decision:** `used_count` increments atomically at redemption; concurrent checkouts cannot exceed `usage_limit`.
- **Consequences:** No over-redemption under load.
