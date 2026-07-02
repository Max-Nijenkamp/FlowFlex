---
domain: projects
module: okrs
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# OKRs — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.okrs.view-any` | View all OKRs |
| `projects.okrs.create` | Create objectives + KRs |
| `projects.okrs.update-own` | Check in / edit own OKRs |
| `projects.okrs.update-any` | Check in / edit anyone's OKRs |

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.okrs.view-any')
        && BillingService::hasModule('projects.okrs');
}
```

## Invariants

- Hierarchy cycles + depth >4 rejected on create/reparent *(assumed depth)*.
- Check-in by a non-owner requires `projects.okrs.update-any`.

## Tenant Isolation

All three tables carry `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. The reminder command runs under `WithCompanyContext` per company. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.okrs')`.

## Encrypted Fields

None. (OKR data is goal metadata; not sensitive PII.)
