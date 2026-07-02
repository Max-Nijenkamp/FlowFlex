---
domain: ecommerce
module: promotions
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Promotions — Architecture

## Services & Actions

`DiscountEngine` is the single entry point for orders/checkout.

| Method | Responsibility |
|---|---|
| `apply(CartData $cart, ?string $couponCode): DiscountResult` | validates coupon (active, window, limits, min order, segment), applies auto-promotions, returns discount lines; throws typed exceptions with user messages ("This code has expired.") |
| `redeem(...)` | atomic `used_count` increment + `ec_coupon_redemptions` row at order paid |

Percent discounts are stored/computed in basis points; fixed in cents. All math via `brick/money`.

## Rule Registry

`ec_promotions.rule` + `discount` are JSONB validated against a rule registry *(assumed: threshold + product/category rules v1; BXGY later)*. Unknown rule types are rejected at save.

## Events

None fired/consumed. `DiscountEngine` is called synchronously by orders/checkout. See [[../../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy | Notes |
|---|---|---|---|
| `CouponResource` | Marketing | simple-resource | usage columns, redemptions relation |
| `EcPromotionResource` | Marketing | simple-resource | rule-builder repeater |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.promotions.view-any')
        && BillingService::hasModule('ecommerce.promotions');
}
```

## Jobs & Scheduling

None. Expiry is evaluated live in `DiscountEngine` from `valid_until`.

## Search & Realtime

None.
