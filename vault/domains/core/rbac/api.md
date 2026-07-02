---
domain: core
module: rbac
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# RBAC — API (DTOs)

Parent: [[_module]] · See also [[architecture]]

RBAC exposes no service contract and **fires/consumes no events** — the three Actions ([[architecture]]) take these two DTOs (`spatie/laravel-data`) as input.

## DTOs

### CreateRoleData (input → `CreateRoleAction`)

| Field | Type | Validation |
|---|---|---|
| name | string | required, unique per company team |
| permissions | string[] | each must exist for the current team's guard |

Consumed by `RoleResource` create/edit; the action persists the role under `team_id = company_id` and `syncPermissions()`.

### AssignRolesData (input → `AssignRolesAction`)

| Field | Type | Validation |
|---|---|---|
| user_id | string (ulid) | required, user in current company |
| roles | string[] | each an existing role name for the team |

Consumed by `UserResource`; the action `syncRoles()` on the target user and throws `CannotRemoveLastOwnerException` when the change would strip the final `owner` (see [[features/last-owner-guard]]).

## Events

None fired, none consumed. RBAC is a management surface over the Spatie tables — cross-domain effects flow only through the resulting permission checks (`canAccess`), not events. See [[../../../architecture/event-bus]].

## Related

- [[_module]] · [[architecture]] · [[security]]
