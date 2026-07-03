---
domain: ecommerce
module: promotions
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Promotions — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.promotions.view-any` | View coupons + promotions |
| `ecommerce.promotions.manage` | Create/edit coupons + promotions, toggle active |

Seeded in `PermissionSeeder`. `redeem` is not a user-facing action — it runs server-side inside `DiscountEngine` during the checkout/order-paid flow, so it needs no panel permission; its rate limit is the public checkout endpoint's (see [[../orders/_module|orders]] / [[../storefront/_module|storefront]] security). See [[../../../../security/authn-authz]].

**Rate limiting:** coupon/promotion CRUD are plain admin writes (no comms, money mutation, files, or external calls) — panel default suffices. Redemption abuse is bounded by the atomic `usage_limit` check and the storefront's public-checkout limiter.

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.promotions.view-any')
        && BillingService::hasModule('ecommerce.promotions');
}
```

## Abuse / Integrity

- Coupon validation (window, limits, min order) is **server-side only** in `DiscountEngine` — the client never decides eligibility.
- `used_count` increments atomically; concurrent redemptions cannot exceed `usage_limit`.
- Per-customer limit enforced via `ec_coupon_redemptions.customer_email`.

## Tenant Isolation

All three tables carry `company_id` (indexed); `CompanyScope` constrains queries; coupon codes are unique per company. See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.promotions')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
