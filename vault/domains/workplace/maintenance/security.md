---
domain: workplace
module: maintenance
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Facility Maintenance — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.maintenance.view-any` | View requests + schedules |
| `workplace.maintenance.report` | Report a request (all users) |
| `workplace.maintenance.assign` | Assign a request to staff/contractor |
| `workplace.maintenance.resolve` | Resolve a request |
| `workplace.maintenance.manage-schedules` | CRUD preventive schedules |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.maintenance.view-any')
        && BillingService::hasModule('workplace.maintenance');
}
```

Row visibility: a plain reporter sees their own requests; facility staff (with `view-any` beyond own) see all.

## File Upload Contract

Before/after photos: **image MIME only** (jpg/png/webp), **max size cap**, stored under `companies/{company_id}/maintenance/` for tenant isolation (security audit 2026-06-11, medium). See [[../../../architecture/security]].

## Tenant Isolation

- Both tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains queries. Schedule runs + photo paths are company-scoped.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('workplace.maintenance')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Requests reference internal staff + free-text contractor names; no external PII requiring encryption *(assumed)*.
