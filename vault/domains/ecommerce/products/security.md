---
domain: ecommerce
module: products
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Products — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.products.view-any` | View products + categories |
| `ecommerce.products.create` | Create products |
| `ecommerce.products.update` | Edit products |
| `ecommerce.products.publish` | Publish (draft → active) |
| `ecommerce.products.manage-categories` | Manage the category tree |

See [[../../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.products.view-any')
        && BillingService::hasModule('ecommerce.products');
}
```

## Upload Contract (security baseline)

Image gallery uploads (Media Library) restrict to image MIME types (jpg/png/webp), enforce a max file size, and store under `companies/{company_id}/`. From [[../../../../build/security-audit-2026-06-11]] (medium). See [[../../../../architecture/security]].

## Tenant Isolation

- `ec_products` + `ec_categories` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- Stock reads/reserves via `StockService` always run under the acting company's context — no side-door into `ops_*` tables.

See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.products')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
