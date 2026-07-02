---
domain: projects
module: tasks
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Tasks — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.tasks.view-any` | View tasks list/records |
| `projects.tasks.view` | View a task (within project membership) |
| `projects.tasks.create` | Create tasks |
| `projects.tasks.update` | Edit tasks + drive status |
| `projects.tasks.delete` | Delete tasks |
| `projects.tasks.comment` | Add comments |

Project membership additionally scopes everything (a task is only visible if the user can see its project).

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.tasks.view-any')
        && BillingService::hasModule('projects.tasks');
}
```

## Content Sanitisation

- Comment + description rich-text bodies are sanitised with **HTMLPurifier** before persistence (XSS prevention). See [[../../../architecture/security]].

## Upload Contract

- Attachments (Media Library): MIME/type whitelist, max file size, tenant-scoped `companies/{id}/` storage path. See [[../../core/file-storage/_module]].

## Tenant Isolation

All four tables carry `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.tasks')`.

## Encrypted Fields

None.
