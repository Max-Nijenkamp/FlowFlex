---
domain: ecommerce
module: promotions
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Promotions & Coupons

Discount codes, automatic promotions, and sales campaigns — fronted by a single `DiscountEngine` that orders/checkout call.

## Module-key

| Field | Value |
|---|---|
| key | `ecommerce.promotions` |
| priority | p3 |
| panel | ecommerce |
| permission-prefix | `ecommerce.promotions` |
| tables | `ec_coupons`, `ec_promotions`, `ec_coupon_redemptions` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../products/_module\|Products]] | product/category conditions |
| Hard | [[../../core/billing/_module\|Billing]] · [[../../core/rbac/_module\|RBAC]] | gating + permissions |
| Soft | [[../../crm/customer-segments/_module\|CRM Segments]] | segment conditions |
| Soft | [[../orders/_module\|Orders]] | redemption at checkout (orders calls `DiscountEngine`) |

## Core Features

- **Coupon code** — code, discount type (percent/fixed), value, usage limits, expiry.
- **Automatic promotions** — rules-based (free shipping over €50; buy-X-get-Y *(assumed: threshold + product rules v1, BXGY later)*).
- **Conditions** — min order value, specific products/categories, customer segment.
- **Usage limits** — total + per-customer (atomic counters).
- **Date-range validity** + stacking rules (one coupon per order; auto-promotions stack *(assumed)*).
- **Usage tracking + redemption report.**
- **`DiscountEngine::apply(cart)`** — the single API orders/checkout call.

## See features/

- [[features/manage-coupons|Manage Coupons]] — coupon + auto-promotion CRUD.
- [[features/apply-discount|Apply Discount]] — the `DiscountEngine` validation + redemption at checkout.

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Coupon validation: expiry, limits (total + per-customer), min order — typed messages.
- [ ] Concurrent redemption respects `usage_limit` (atomic).
- [ ] Percent (basis points) + fixed math via `brick/money`.
- [ ] One coupon per order; auto-promotions stack.
- [ ] Free-shipping-over-threshold rule.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `DiscountEngine::apply` / `redeem` | ecommerce.orders / storefront | Orders calls in; promotions writes only its own tables |
| Reads | segment membership | crm.customer-segments | Segment conditions (soft) |

**Data ownership:** `ecommerce.promotions` writes only `ec_coupons` + `ec_promotions` + `ec_coupon_redemptions`. Orders calls `DiscountEngine`; promotions never writes `ec_orders` ([[../../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../orders/_module|Orders]] · [[../products/_module|Products]] · [[../../crm/referral-program/_module|Referral Program]]
- [[../../../glossary]]
