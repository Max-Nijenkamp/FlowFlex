---
domain: ecommerce
module: storefront
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Storefront — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.storefront.manage` | Configure settings + content pages |

See [[../../../../security/authn-authz]].

## Access Contract

Admin config:

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.storefront.manage')
        && BillingService::hasModule('ecommerce.storefront');
}
```

## Public Surface Guard

- Public storefront (browse/product/cart/checkout/confirmation) runs on the **guest guard**, scoped to the company via `{company-slug}`.
- Only `status = active` products and `is_published` pages are exposed; drafts/archived never leak.
- **Cart re-validated server-side** at every step — stale price/stock rejected; the client cart is never trusted.
- Content-page bodies purified (htmlpurifier).

See [[../../../../architecture/security]].

## Tenant Isolation

`ec_storefront_pages` + settings carry/scope by `company_id`; the public slug resolves the company; `CompanyScope` constrains queries. See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.storefront')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
