---
domain: ecommerce
module: variants
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Variants — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.variants.manage` | Define options, generate + bulk-edit variants (under the products umbrella) |

Seeded in `PermissionSeeder`. See [[../../../../security/authn-authz]].

**Rate limiting:** the "Generate variants" and bulk-edit actions write only catalogue rows (no comms, money mutation, file generation, or external calls) — panel default suffices, no dedicated limiter.

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.variants.manage')
        && BillingService::hasModule('ecommerce.variants');
}
```

## Tenant Isolation

`ec_product_options` + `ec_variants` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries. See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.variants')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
