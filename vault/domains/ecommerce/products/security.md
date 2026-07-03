---
domain: ecommerce
module: products
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Products — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.products.view-any` | View products + categories |
| `ecommerce.products.create` | Create products |
| `ecommerce.products.update` | Edit products |
| `ecommerce.products.delete` | Soft-delete a product *(assumed)* |
| `ecommerce.products.publish` | Publish (draft → active) |
| `ecommerce.products.archive` | Archive (active → archived) + re-activate *(assumed)* |
| `ecommerce.products.manage-categories` | Manage the category tree |

Seeded in `PermissionSeeder`. See [[../../../../security/authn-authz]].

**Rate limiting:** publish and archive are plain status flips (no comms, money, files, or external calls) — no dedicated limiter required beyond the panel default. Bulk import runs through `core.import` and inherits its `exports`/import limiter *(assumed)*.

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.products.view-any')
        && BillingService::hasModule('ecommerce.products');
}
```

## Upload Contract (security baseline)

Image gallery uploads (Media Library) restrict to image MIME types (jpg/png/webp), enforce a max file size, and store under `companies/{company_id}/`. From [[../../../../_archive/build-history/security-audit-2026-06-11]] (medium). See [[../../../../architecture/security]].

## Tenant Isolation

- `ec_products` + `ec_categories` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- Stock reads/reserves via `StockService` always run under the acting company's context — no side-door into `ops_*` tables.

See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.products')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
