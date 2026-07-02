---
domain: ecommerce
module: promotions
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Promotions — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.promotions.view-any` | View coupons + promotions |
| `ecommerce.promotions.manage` | Create/edit coupons + promotions |

See [[../../../../security/authn-authz]].

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
