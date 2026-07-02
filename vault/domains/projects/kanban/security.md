---
domain: projects
module: kanban
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Kanban — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.kanban.view` | Open the board |

Card mutations are gated by the underlying `projects.tasks.update` (moves go through `MoveTask`).

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.kanban.view')
        && BillingService::hasModule('projects.kanban');
}
```

## Tenant Isolation

Reads flow through `KanbanService`, which queries `proj_tasks` under `CompanyScope` + project-membership scope. No side-door writes — moves reuse the tasks action. See [[../../../security/tenancy-isolation]].

## Broadcast Channel

`company.{id}.projects` is a private, company-scoped Reverb channel; authorisation checks company membership. See [[../../../architecture/websockets]].

## Encrypted Fields

None (owns no data).
