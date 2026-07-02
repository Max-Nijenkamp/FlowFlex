---
domain: projects
module: projects
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Projects — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.projects.view-any` | See all projects (bypasses membership scope) |
| `projects.projects.view` | View a project you are a member of |
| `projects.projects.create` | Create projects |
| `projects.projects.update` | Edit + drive state machine |
| `projects.projects.delete` | Delete/archive projects |
| `projects.projects.manage-members` | Add/remove members, set roles |

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.projects.view-any')
        && BillingService::hasModule('projects.projects');
}
```

## Membership Scoping

Beyond company scope, listing is **member-scoped**: a user sees only projects they belong to, unless they hold `projects.projects.view-any`. Enforced in `ProjectService` query scope, not just the UI.

## Tenant Isolation

`proj_projects` + `proj_project_members` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.projects')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
