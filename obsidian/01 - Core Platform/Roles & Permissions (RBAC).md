---
tags: [flowflex, core, rbac, permissions, roles, spatie, phase/1]
domain: Core Platform
panel: workspace
color: "#2199C8"
status: built
last_updated: 2026-05-06
---

# Roles & Permissions (RBAC)

Granular access control built on Spatie Laravel Permission. Every panel, resource, and action is gated through this module.

**Who uses it:** Workspace admins, team managers
**Filament Panel:** `workspace`
**Depends on:** [[Authentication & Identity]]
**Build complexity:** Medium — 2 resources, 3 tables (Spatie)

## Events Fired

- `RoleCreated` — audit log
- `PermissionGranted` — audit log with user + permission + granter
- `PermissionRevoked` — audit log

## Permission Naming Convention

All permissions follow: `{module}.{resource}.{action}`

**Panel-level:** `hr.panel.access`, `finance.panel.access`
**Resource-level:** `hr.employees.view`, `hr.employees.create`, `hr.employees.edit`, `hr.employees.delete`
**Field-level:** `hr.employees.salary.view`, `hr.employees.salary.edit`

## Permission Layers

Permissions operate at three distinct levels:

### 1. Panel-Level
Can the user access this Filament panel at all?

```
hr.panel.access
finance.panel.access
crm.panel.access
```

### 2. Resource-Level
Can the user perform CRUD on a resource within a panel?

```
hr.employees.view
hr.employees.create
hr.employees.edit
hr.employees.delete
finance.invoices.view
finance.invoices.send
```

### 3. Field-Level
Can the user see or edit a specific field on a record?

```
hr.employees.salary.view
hr.employees.salary.edit
hr.employees.national_insurance.view
```

## Role Builder

- Create custom roles with any combination of permissions
- Role naming and description
- Role inheritance (a Senior Manager role extends Manager role)
- Assign roles to users individually or in bulk
- Clone existing roles as starting point

### System Roles (cannot be deleted)

| Role | Description |
|---|---|
| Owner | Full access, billing access, cannot be removed |
| Admin | Full access to all modules activated for this tenant |
| Member | Base access, must be granted module permissions explicitly |
| Read-only | View-only across all permitted modules |

## Scoping & Restrictions

- **Department scoping** — a manager sees only their department's records
- **Team scoping** — a team lead sees only their team
- **Location scoping** — a regional manager sees only their region
- **IP allowlist per role** — certain roles can only log in from specific IPs
- **Time-based access windows** — contractors can only access Mon-Fri 09:00-18:00
- **Temporary access grants** — with automatic expiry date

## Permission Matrix UI

- Grid view: roles (columns) vs permissions (rows) — toggle cells
- Permission search and filter
- "Effective permissions" view — shows what a specific user can actually do, including inherited from role hierarchy

## Implementation in Filament Resources

Every Filament resource **must** implement these methods:

```php
public static function canViewAny(): bool
{
    return auth()->user()->can('hr.employees.view');
}

public static function canCreate(): bool
{
    return auth()->user()->can('hr.employees.create');
}

public static function canEdit(Model $record): bool
{
    return auth()->user()->can('hr.employees.edit');
}

public static function canDelete(Model $record): bool
{
    return auth()->user()->can('hr.employees.delete');
}
```

Never rely on Filament default — always implement explicitly. See [[Security Rules]].

## Database Tables (3 — Spatie standard)

1. `permissions` — all permission definitions
2. `roles` — role definitions per tenant
3. `model_has_roles` — pivot: user → role assignments
4. `model_has_permissions` — pivot: direct user → permission assignments
5. `role_has_permissions` — pivot: role → permissions

## Related

- [[Authentication & Identity]]
- [[Security Rules]]
- [[Module Development Checklist]]
- [[Panel Map]]
- [[Workspace Panel]]
