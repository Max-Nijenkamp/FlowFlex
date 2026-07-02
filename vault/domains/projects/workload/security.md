---
domain: projects
module: workload
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Workload — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.workload.view` | Open the workload heat-map |

Drag mutations gated by `projects.tasks.update` (via `UpdateTaskAction`).

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.workload.view')
        && BillingService::hasModule('projects.workload');
}
```

## Tenant Isolation

`WorkloadService` aggregates `proj_tasks` under `CompanyScope`; HR capacity read under the same company scope. No side-door writes (mutations reuse the tasks action). See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None (owns no data). Reads only non-sensitive task/capacity fields.
