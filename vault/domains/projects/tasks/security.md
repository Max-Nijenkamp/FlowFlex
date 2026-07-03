---
domain: projects
module: tasks
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

**Verb-per-command:** every status-machine transition (`todo → in_progress → in_review → done` / `cancelled`, reopen — including `MoveTask` and `CompleteTaskAction`) is authorized by `projects.tasks.update`; the spec deliberately routes all transitions through one verb rather than a per-state permission (lightweight machine, per [[architecture]]). The only distinct command is commenting (`projects.tasks.comment`). `@mention` notifications dispatch via events to core.notifications and carry no separate permission — a mention only notifies a user who can already see the task.

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
