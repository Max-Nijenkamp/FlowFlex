---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.promotions
status: planned
color: "#4ADE80"
---

# Promotions & Coupons

Discount codes, automatic promotions, and sales campaigns.

## Core Features

- Coupon code: code, discount type (percent/fixed), value, usage limits, expiry
- Automatic promotions: rules-based (e.g. buy 2 get 1, free shipping over €50)
- Conditions: minimum order value, specific products/categories, customer segment
- Usage limits: total uses, per-customer uses
- Date range validity
- Stacking rules (can/cannot combine coupons)
- Usage tracking and redemption report

## Data Model

| Table | Key Columns |
|---|---|
| `ec_coupons` | company_id, code, discount_type, discount_value, min_order_cents, usage_limit, used_count, per_customer_limit, valid_from, valid_until, is_active |
| `ec_promotions` | company_id, name, rule (json), discount (json), valid_from, valid_until, is_active |
| `ec_coupon_redemptions` | coupon_id, company_id, order_id, customer_contact_id, redeemed_at |

## Filament

**Nav group:** Marketing

- `CouponResource` — create, edit, track usage
- `PromotionResource` — rule-based promotion builder

## Related

- [[domains/ecommerce/orders]]
- [[domains/ecommerce/products]]
