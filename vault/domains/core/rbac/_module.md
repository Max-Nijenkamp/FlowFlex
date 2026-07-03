---
domain: core
module: rbac
type: module
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Roles & Permissions

Role management UI for company admins. Create custom roles, assign permissions per module, and manage user role assignments. Backed by `spatie/laravel-permission` (team scoping `team_id = company_id`) with `bezhansalleh/filament-shield` for the Filament UI. Always-free core module.

## Module-key

`core.rbac`

**Priority:** v1-core  
**Panel:** app  
**Permission prefix:** `core.rbac`  
**Tables:** none of its own — uses the four Spatie permission tables (`roles`, `permissions`, `model_has_roles`, `role_has_permissions`), `team_id = company_id`  
**Events:** fires none · consumes `ModuleActivated`, `ModuleDeactivated` (recompute assignable-permission set) · `OwnershipTransferred` fired on transfer *(assumed)*

## Core Features

- Built-in roles: `owner` (singular — see below), `admin`, `manager`, `employee`.
- **Exactly one owner per company.** Ownership is a single, transferable seat — never duplicated. Canonical
  owner = the sole user holding the `owner` role, denormalised to `companies.owner_user_id` *(assumed)*.
  Transferring ownership demotes the previous owner in the same atomic action. See [[features/ownership]].
- **Owner can create any role**, but can **only assign permissions for modules the company currently has
  active** ([[../../../infrastructure/module-catalog]]). Deactivating a module drops its permissions from the
  assignable set and suspends them on existing roles until reactivated. See [[features/module-scoped-permissions]].
- Custom role creation; permissions grouped by domain in the UI (only active-module groups shown).
- User role assignment — one or more roles per user (union of permissions).
- Permission format `domain.module.action`; panel/domain access `access.{domain}`.
- `filament-shield` generates the permission matrix from discovered Filament resources.
- Role changes bust the Spatie permission cache automatically on `syncPermissions` / `assignRole` (see [[../../../architecture/caching]]).

## Data & Relations

- **Owns/writes**: the four Spatie permission tables (`team_id = company_id`). **Reads**: the active-module
  set to bound assignable permissions. **Never** writes another domain's tables ([[../../../security/data-ownership]]).
- Consumes `ModuleActivated` / `ModuleDeactivated` (core.billing) → recompute assignable-permission set.
- Feeds every other module: the permission strings their `canAccess()` consults.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.panels | `/app` resources |
| Hard | foundation.permissions | permission strings exist to assign |
| Soft | [[../invitation-system/_module]] | `UserResource` "invite" action; hidden without it |

## Sibling notes

- [[architecture]] — actions + role/permission flow diagram
- [[data-model]] — Spatie permission tables + ERD
- [[api]] — `CreateRoleData`, `AssignRolesData` DTOs
- [[security]] — permissions, owner guardrails, team scoping
- [[features/custom-roles]] · [[features/ownership]] · [[features/module-scoped-permissions]] · [[features/last-owner-guard]]

## Related

- [[../company-settings/_module]] · [[../invitation-system/_module]]
- [[../../../architecture/patterns/policy]] · [[../../../glossary]]

## Build Manifest (flat paths, verified)

```
app/Data/{CreateRoleData,AssignRolesData}.php
app/Actions/{CreateRoleAction,AssignRolesAction,DeleteRoleAction}.php
app/Exceptions/{CannotRemoveLastOwnerException,CannotDeleteBuiltInRoleException}.php
app/Filament/App/Resources/RoleResource.php  (+ Pages/)
app/Filament/App/Resources/UserResource.php  (+ Pages/)
tests/Feature/Core/{RoleManagementTest,RoleIsolationTest}.php
```

> Spec listed `app/Data/Core/...`, `app/Actions/Core/...`, `app/Exceptions/Core/...`. Real layout is **flat** (no `Core/` subdir) — corrected above. The two exception classes were confirmed present in `app/Exceptions/`.
>
> `TransferOwnershipAction` and the `OwnershipTransferred` event are referenced by [[features/ownership]] and [[security]] but are **not yet in this manifest** *(assumed — pending build confirmation)*.

## Test Checklist

- [ ] Tenant isolation: company A's roles/assignments are invisible and unusable to company B (`team_id = company_id`); role names may repeat across companies
- [ ] Module gating: n/a (platform module, always active — always-free core)
- [ ] Demoting/removing the sole `owner` throws `CannotRemoveLastOwnerException`; ownership only changes via atomic transfer
- [ ] Deleting a built-in role (`owner`/`admin`/`manager`/`employee`) throws `CannotDeleteBuiltInRoleException`
- [ ] A permission for an inactive module cannot be granted (server-side reject in `CreateRoleAction`/`AssignRolesAction`)
- [ ] `ModuleDeactivated` suspends that module's grants (kept inert); reactivation restores them
- [ ] `syncPermissions`/`assignRole` busts the Spatie permission cache within the same request
