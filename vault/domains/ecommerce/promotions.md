---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.promotions
status: planned
priority: p3
depends-on: [ecommerce.products, core.billing, core.rbac]
soft-depends: [crm.segments, ecommerce.orders]
fires-events: []
consumes-events: []
patterns: [money]
tables: [ec_coupons, ec_promotions, ec_coupon_redemptions]
permission-prefix: ecommerce.promotions
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Promotions & Coupons

Discount codes, automatic promotions, and sales campaigns.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ecommerce/products\|ecommerce.products]] | product/category conditions |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/crm/customer-segments\|crm.segments]] | segment conditions |
| Soft | [[domains/ecommerce/orders\|ecommerce.orders]] | redemption at checkout (orders calls `DiscountEngine`) |

---

## Core Features

- Coupon code: code, discount type (percent/fixed), value, usage limits, expiry
- Automatic promotions: rules-based (e.g. free shipping over €50; buy-X-get-Y *(assumed: threshold + product rules v1, BXGY later)*)
- Conditions: minimum order value, specific products/categories, customer segment
- Usage limits: total uses, per-customer uses (atomic counters)
- Date range validity
- Stacking rules: one coupon per order; auto-promotions stack with coupon *(assumed)*
- Usage tracking and redemption report
- `DiscountEngine::apply(cart): DiscountResult` — single API orders/checkout call

---

## Data Model

### ec_coupons

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| code | string | unique per company, case-insensitive |
| discount_type | string | percent / fixed |
| discount_value | int | basis points for percent / cents for fixed |
| min_order_cents | bigint nullable | |
| usage_limit / per_customer_limit | int nullable | |
| used_count | int default 0 | atomic increment |
| valid_from / valid_until | date nullable | |
| is_active | boolean | |
| deleted_at | timestamp nullable | |

### ec_promotions — id, company_id (indexed), name, rule (jsonb registry-validated), discount (jsonb), valid_from/valid_until, is_active
### ec_coupon_redemptions — id, coupon_id FK, company_id, order_id FK, customer_email, redeemed_at

---

## DTOs

### CreateCouponData — code (unique, alphanumeric-dash), discount_type + value (percent ≤ 10000bp), min_order_cents?, limits, validity (until ≥ from)

## Services & Actions

- `DiscountEngine::apply(CartData $cart, ?string $couponCode): DiscountResult` — validates coupon (active, window, limits, min order, segment), applies auto-promotions, returns discount lines; throws typed exceptions with user messages ("This code has expired.")
- `DiscountEngine::redeem(...)` — atomic counter + redemption row at order paid

---

## Filament

**Nav group:** Marketing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CouponResource` | #1 CRUD resource | usage columns, redemptions relation |
| `EcPromotionResource` | #1 CRUD resource | rule builder repeater |

---

## Permissions

`ecommerce.promotions.view-any` · `ecommerce.promotions.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Coupon validation: expiry, limits (total + per-customer), min order — typed messages
- [ ] Concurrent redemption respects usage_limit (atomic)
- [ ] Percent (basis points) + fixed math via brick/money
- [ ] One coupon per order; auto-promotions stack
- [ ] Free-shipping-over-threshold rule

---

## Build Manifest

```
database/migrations/xxxx_create_ec_coupons_table.php
database/migrations/xxxx_create_ec_promotions_table.php
database/migrations/xxxx_create_ec_coupon_redemptions_table.php
app/Models/Ecommerce/{Coupon,EcPromotion,CouponRedemption}.php
app/Data/Ecommerce/{CreateCouponData,DiscountResult}.php
app/Services/Ecommerce/DiscountEngine.php
app/Filament/Ecommerce/Resources/{CouponResource,EcPromotionResource}.php
database/factories/Ecommerce/CouponFactory.php
tests/Feature/Ecommerce/{DiscountEngineTest,CouponLimitTest}.php
```

---

## Related

- [[domains/ecommerce/orders]]
- [[domains/ecommerce/products]]
- [[domains/crm/referral-program]]
