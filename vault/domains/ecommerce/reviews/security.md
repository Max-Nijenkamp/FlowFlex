---
domain: ecommerce
module: reviews
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Reviews — Security

## Permissions

| Permission | Grants |
|---|---|
| `ecommerce.reviews.view-any` | View reviews / moderation queue |
| `ecommerce.reviews.moderate` | Approve / reject |
| `ecommerce.reviews.reply` | Post merchant replies |

See [[../../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.reviews.view-any')
        && BillingService::hasModule('ecommerce.reviews');
}
```

## Public Submission Guard (HIGH)

The public submission + helpful-vote routes run on the **public/guest guard** with **signed-URL** validation of `review_token`, distinct from the authenticated Filament panel guard. Bodies/titles are purified (htmlpurifier); routes are rate-limited (`throttle:public`). From [[../../../../build/security-audit-2026-06-11]] (HIGH). See [[../../../../architecture/security]].

## Tenant Isolation

`ec_reviews` carries `company_id` (indexed); `CompanyScope` constrains queries; the signed link resolves the company from the review/token. See [[../../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('ecommerce.reviews')`. See [[../../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
