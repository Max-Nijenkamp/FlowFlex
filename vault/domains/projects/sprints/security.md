---
domain: projects
module: sprints
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Sprints — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.sprints.view-any` | View sprints/board |
| `projects.sprints.manage` | Create, start, complete sprints; edit retro |
| `projects.sprints.assign-tasks` | Add/remove tasks to/from a sprint |

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.sprints.view-any')
        && BillingService::hasModule('projects.sprints');
}
```

## Invariants (service-enforced)

- One active sprint per project (`ActiveSprintExistsException`).
- A task sits in at most one active sprint.

## Tenant Isolation

`proj_sprints` + `proj_sprint_tasks` carry `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.sprints')`.

## Encrypted Fields

None.
