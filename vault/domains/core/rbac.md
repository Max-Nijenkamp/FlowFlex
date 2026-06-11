---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.rbac
status: complete
priority: v1-core
depends-on: [foundation.panels, foundation.permissions]
soft-depends: [core.invitations]
fires-events: []
consumes-events: []
patterns: [policy, seeding]
tables: []
permission-prefix: core.rbac
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Roles & Permissions

Role management UI for company admins. Create custom roles, assign permissions per module, manage user role assignments. Backed by `spatie/laravel-permission` with `bezhansalleh/filament-shield` for the Filament UI. Always-free core module.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/filament-panels\|foundation.panels]] | `/app` resources |
| Hard | [[domains/foundation/permissions-seed\|foundation.permissions]] | permission strings exist to assign |
| Soft | [[domains/core/invitation-system\|core.invitations]] | UserResource "invite" action; hidden without it |

---

## Core Features

- Built-in roles: `owner`, `admin`, `manager`, `employee` (see [[architecture/auth-rbac]])
- Custom role creation with any permission combination
- Permission assignment UI per role — permissions grouped by domain
- User role assignment — one or more roles per user
- Permission format: `domain.module.action` (e.g. `hr.employees.create`)
- Panel-level access permissions: `access.hr-panel`, `access.finance-panel`, etc.
- `filament-shield` generates permission UI from discovered Filament resources
- Role changes take effect within the Spatie permission cache window (1h max, busted automatically on `syncPermissions`/`assignRole` — [[architecture/caching]])
- Company owner role cannot be removed or deleted; the last owner cannot be demoted

---

## Data Model

Spatie Permission tables — no custom tables:

| Table | Purpose |
|---|---|
| `permissions` | All permission strings, scoped by `team_id = company_id` |
| `roles` | Named roles per company team |
| `model_has_roles` | User → role assignments |
| `role_has_permissions` | Role → permission assignments |

---

## DTOs

### CreateRoleData (input)
| Field | Type | Validation |
|---|---|---|
| name | string | required, max:100, unique per company team |
| permissions | array<string> | each exists in `permissions` |

### AssignRolesData (input)
| Field | Type | Validation |
|---|---|---|
| user_id | string | required, ulid, in company |
| roles | array<string> | each exists; cannot remove `owner` from last owner |

## Services & Actions

Actions (simple ops):
- `CreateRoleAction::run(CreateRoleData $data): Role`
- `AssignRolesAction::run(AssignRolesData $data): void` — throws `CannotRemoveLastOwnerException`
- `DeleteRoleAction::run(string $roleId): void` — throws `CannotDeleteBuiltInRoleException`, refuses when users still assigned *(assumed)*

---

## Filament

**Nav group:** Team

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `RoleResource` | #1 CRUD resource | shield-generated permission matrix grouped by domain |
| `UserResource` | #1 CRUD resource | list users, assign roles, deactivate; invite action (soft-dep) |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('core.rbac.view-any') && BillingService::hasModule('core.rbac')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`core.rbac.view-any` · `core.rbac.view` · `core.rbac.create` · `core.rbac.update` · `core.rbac.delete` · `core.rbac.assign`

---

## Test Checklist

- [ ] Tenant isolation: roles from company A invisible in company B (team scoping)
- [ ] Custom role with subset of permissions gates resources correctly
- [ ] Last owner cannot be demoted/deleted (`CannotRemoveLastOwnerException`)
- [ ] Built-in roles cannot be deleted
- [ ] Permission cache busts after role change (access reflects within request)
- [ ] User with multiple roles gets union of permissions

---

## Build Manifest

```
app/Data/Core/{CreateRoleData,AssignRolesData}.php
app/Actions/Core/{CreateRoleAction,AssignRolesAction,DeleteRoleAction}.php
app/Exceptions/Core/{CannotRemoveLastOwnerException,CannotDeleteBuiltInRoleException}.php
app/Filament/App/Resources/{RoleResource,UserResource}.php
tests/Feature/Core/{RoleManagementTest,RoleIsolationTest}.php
```

---

## Related

- [[architecture/auth-rbac]]
- [[architecture/patterns/policy]]
- [[domains/core/invitation-system]]
- [[domains/core/company-settings]]
