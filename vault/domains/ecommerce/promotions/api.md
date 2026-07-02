---
domain: ecommerce
module: promotions
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Promotions — API / DTOs

## `CreateCouponData`

| Field | Type | Rules |
|---|---|---|
| `code` | string | unique per company, alphanumeric-dash |
| `discount_type` | enum | percent / fixed |
| `discount_value` | int | percent ≤ 10000 bp; fixed in cents, min:0 |
| `min_order_cents` | int | nullable |
| `usage_limit` | int | nullable |
| `per_customer_limit` | int | nullable |
| `valid_from` / `valid_until` | date | nullable, `until ≥ from` |

## `DiscountResult` (output)

Returned by `DiscountEngine::apply`: discount lines (coupon + auto-promotions), total discount cents, and any user-facing rejection message.

## `DiscountEngine`

- `apply(CartData $cart, ?string $couponCode): DiscountResult`
- `redeem(...)` — atomic counter + redemption row at order paid.

## Public / Portal Endpoints

None directly. Coupon entry happens in the storefront checkout (Vue + Inertia), which posts the code as part of `CartData`; validation runs server-side via `DiscountEngine`.
