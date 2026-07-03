---
domain: core
module: rbac
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# RBAC — Security

Parent: [[_module]]

RBAC is itself the authorization backbone: it produces the permission strings every other module's `canAccess()` consults. Its own surfaces are the most sensitive in `/app`.

## Permissions

| Permission | Grants |
|---|---|
| `core.rbac.view-any` | Role & User list surfaces (`RoleResource`, `UserResource`) |
| `core.rbac.create` | Create a role (`CreateRoleAction`) |
| `core.rbac.update` | Edit a role / sync its permissions |
| `core.rbac.delete` | Delete a custom role (`DeleteRoleAction`) — built-ins refused |
| `core.rbac.assign-roles` | Assign / remove a user's roles (`AssignRolesAction`) |
| `core.rbac.transfer-ownership` | Transfer company ownership (`TransferOwnershipAction`) — **owner only** |

One permission per command action; the `owner` role implicitly holds all of them. Seeded in `PermissionSeeder`. `core.rbac` is a platform module (always active) — no module-gating verb. These are all in-panel actions with no comms / money / file / external-API side effect, so no dedicated rate limiter applies (the invite action's limiter is owned by invitation-system).

## Permissions model

- Permission format `domain.module.action`; panel access as `access.{domain}-panel`.
- Built-in roles: `owner`, `admin`, `manager`, `employee`. `owner` holds every permission for the company.
- Custom roles hold any subset **of active-module permissions** ([[features/module-scoped-permissions]]); a user's effective permissions are the **union** across all assigned roles.
- Role-management surfaces (`RoleResource`, `UserResource`) gate on `canAccess()` per [[../../../architecture/patterns/policy]] — owner + `core.rbac.*` holders only. See [[../../../security/authn-authz]].

## Owner guardrails

- **Exactly one owner** per company — never zero, never two. Changing owner is a **transfer** only
  ([[features/ownership]]): `TransferOwnershipAction` assigns `owner` to the new user and demotes the old one
  atomically. A second `owner` assignment is rejected; the sole owner cannot be demoted except by transfer
  (`CannotRemoveLastOwnerException`).
- **Module-scoped assignment**: a permission whose module is inactive cannot be granted — enforced in
  `CreateRoleAction`/`AssignRolesAction`, not just hidden ([[features/module-scoped-permissions]]).
- **Built-in roles cannot be deleted** — `DeleteRoleAction` throws `CannotDeleteBuiltInRoleException` for `owner`/`admin`/`manager`/`employee`.

Detail: [[features/ownership]] · [[features/last-owner-guard]].

## Team scoping

Every role, permission and assignment is scoped `team_id = company_id` (Spatie teams feature). Company A's roles are invisible to and unusable by company B — a role name may repeat across companies without collision. This is the tenant boundary for authorization; see [[../../../security/tenancy-isolation]] and [[data-model]].

## Permission cache

Spatie caches the permission registry. `syncPermissions()` / `assignRole()` / `syncRoles()` bust the cache automatically, so role changes take effect within the same request. See [[../../../architecture/caching]].

## Related

- [[_module]] · [[data-model]] · [[api]]
- [[../../../security/authn-authz]] · [[../../../architecture/patterns/policy]] · [[../../../security/tenancy-isolation]]
