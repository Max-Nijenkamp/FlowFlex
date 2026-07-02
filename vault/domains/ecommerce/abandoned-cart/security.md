---
domain: ecommerce
module: abandoned-cart
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Abandoned Cart — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.abandoned-cart.view` | View abandoned carts + recovery funnel |

See [[../../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.abandoned-cart.view')
        && BillingService::hasModule('ecommerce.abandoned-cart');
}
```

## Public Restore Guard (HIGH)

- The restore-cart route uses **Laravel signed URLs** (`signed` middleware) validating `recovery_token` on the **public/guest guard**; the token is a **single-use capability token**. From [[../../../../build/security-audit-2026-06-11]] (HIGH).
- **Rate limiter** (`throttle:public`) on the public restore route (medium).

See [[../../../../architecture/security]].

## Privacy / GDPR

- Carts are purged after 90 days *(assumed)* by `PruneCartsCommand`.
- Marketing suppression / opt-out honored *(assumed: own opt-out link v1)*. See [[../../../../architecture/data-lifecycle]].

## Tenant Isolation

`ec_carts` + `ec_cart_recovery_emails` carry `company_id` (indexed); `CompanyScope` constrains queries; scheduled commands run under company context per cart. See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.abandoned-cart')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None. `customer_email` stored plaintext *(assumed)*.
