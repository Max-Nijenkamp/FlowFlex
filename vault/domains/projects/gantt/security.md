---
domain: projects
module: gantt
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Gantt — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.gantt.view` | Open the Gantt chart |

Drag mutations gated by `projects.tasks.update` (via `UpdateTaskAction`).

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.gantt.view')
        && BillingService::hasModule('projects.gantt');
}
```

## Tenant Isolation

`GanttService` reads under `CompanyScope` + project-membership scope; mutations reuse the tasks action (no side-door). See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None (owns no data).
