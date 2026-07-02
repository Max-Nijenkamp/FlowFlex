---
domain: ecommerce
module: promotions
feature: apply-discount
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Apply Discount

Validate a coupon and layer automatic promotions at checkout via `DiscountEngine`, then record the redemption at order-paid.

## Behaviour

1. Checkout calls `DiscountEngine::apply(cart, couponCode)`.
2. Coupon validated: active, within window, under total + per-customer limits, meets min order, matches segment/product conditions. Failures throw typed exceptions with user messages ("This code has expired.").
3. Automatic promotions (threshold/product/category) layer on; one coupon per order, auto-promotions stack *(assumed)*.
4. Returns a `DiscountResult` (discount lines + total) the order applies.
5. At order-paid, `redeem` atomically increments `used_count` and writes an `ec_coupon_redemptions` row.

## UI

- **Kind**: background (server-side engine) — surfaced in the storefront checkout coupon field.
- **Page**: no admin page; the coupon input lives on the storefront checkout (Vue + Inertia, [[../../storefront/_module|storefront]]).
- **Key interactions**: shopper enters code → checkout calls `apply` → discount line shown or rejection message; discount recalculated if cart changes.
- **States**: empty (no code) · loading (validating) · error (typed message: expired / limit reached / min order not met) · selected (discount line applied).
- **Gating**: public checkout (guest); validation entirely server-side.

## Data

- Owns / writes: `ec_coupons` (`used_count`), `ec_coupon_redemptions` only.
- Reads: cart (storefront), segment membership (crm.customer-segments, soft), product/category conditions.
- Cross-domain writes: NONE — the discount is returned to Orders, which applies it to its own totals; promotions never writes `ec_orders` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: cart from checkout ([[../../storefront/_module|storefront]] / [[../../orders/_module|orders]]).
- Feeds: `DiscountResult` → order totals; redemption at order-paid.
- Shared entity: `ec_orders` (redemption links), segments (crm).

## Unknowns

- Segment-conditioned promotions when CRM inactive (see [[../unknowns]]).

## Related

- [[../_module|Promotions & Coupons]] · [[manage-coupons]] · [[../../orders/_module|Orders]]
