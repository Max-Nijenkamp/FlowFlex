---
domain: workplace
module: desk-booking
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Desk Booking — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.desks.view-any` | View desks + floor map |
| `workplace.desks.book` | Book a desk (all users) |
| `workplace.desks.manage` | CRUD desks, edit any booking |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.desks.view-any')
        && BillingService::hasModule('workplace.desks');
}
```

## Tenant Isolation

- `wp_desks` + `wp_desk_bookings` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- Dual-uniqueness checks and the floor map are scoped to the acting company.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('workplace.desks')` gates panel access. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Bookings reference internal employees; no external PII.

## Team-view Privacy

The team view exposes where colleagues sit on a given day. This is intra-company only (`company_id`-scoped) and shows same-day bookings; whether an employee can opt out of appearing is an open question — see [[unknowns]].
