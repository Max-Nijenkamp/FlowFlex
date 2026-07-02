---
domain: projects
module: resource-allocation
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Resource Allocation — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.resources.view-any` | View allocations + timeline |
| `projects.resources.manage` | Create/edit/delete allocations |

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.resources.view-any')
        && BillingService::hasModule('projects.resources');
}
```

## Tenant Isolation

`proj_resource_allocations` carries `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. Utilisation reads time entries under the same company scope. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.resources')`.

## Encrypted Fields

None. (Allocation data is capacity-planning metadata, not sensitive PII.)
