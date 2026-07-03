---
domain: crm
module: price-management
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Price Management — Security

## Permissions

| Permission | Grants |
|---|---|
| `crm.pricing.view-any` | Access catalogue, price books, discounts. |
| `crm.pricing.manage-products` | Create / edit products. |
| `crm.pricing.manage-price-books` | Create / edit price books, entries, and volume discounts. |
| `crm.pricing.assign-book` | Assign a price book to an account or segment (`AssignPriceBookAction`). |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.pricing.view-any')
        && hasModule('crm.pricing');
}
```

See [[../../../security/authn-authz]].

## Tenant Isolation

All four tables carry an indexed `company_id` scoped via `BelongsToCompany` / `CompanyScope`. SKU uniqueness, price-book names, and the single-default invariant are all scoped per company. `PricingService::resolve()` only reads within the current tenant. See [[../../../security/tenancy-isolation]].

## Module Gating

Panel artifacts gated behind `hasModule('crm.pricing')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Prices and costs are commercial config, not personal data.

## Source Security Notes

- Authorization via spatie/laravel-permission, not policies — see [[../../../security/authn-authz]].
- Cost and margin data is company-confidential; `manage-price-books` should be role-restricted to avoid exposing `cost_cents` to line reps. See [[../../../security/threat-model]].
