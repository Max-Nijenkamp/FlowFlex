---
domain: core
module: rbac
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# RBAC — Architecture

Simple-ops pattern: three Actions (`lorisleiva/laravel-actions`) over the Spatie permission tables. No service layer — Filament resources call the Actions directly.

## Actions (`app/Actions/`)

| Action | Signature | Notes |
|---|---|---|
| `CreateRoleAction` | `run(CreateRoleData): Role` | creates role under the current company team, syncs permissions |
| `AssignRolesAction` | `run(AssignRolesData): void` | throws `CannotRemoveLastOwnerException` |
| `DeleteRoleAction` | `run(string $roleId): void` | throws `CannotDeleteBuiltInRoleException`; refuses while users assigned *(assumed)* |

## Exceptions (`app/Exceptions/`)

- `CannotRemoveLastOwnerException` — guards demotion/removal of the final `owner`.
- `CannotDeleteBuiltInRoleException` — guards deletion of `owner`/`admin`/`manager`/`employee`.

## Filament surface

- `RoleResource` — shield-generated permission matrix grouped by domain.
- `UserResource` — list users, assign roles, deactivate; invite action (soft-dep on invitations).

## Role / permission flow

```mermaid
flowchart TD
    A[Admin in /app Team nav] -->|create role| B[RoleResource form]
    B --> C[CreateRoleAction]
    C -->|syncPermissions| D[(role_has_permissions)]
    C --> E[(roles, team_id=company_id)]

    F[Admin opens UserResource] -->|assign roles| G[AssignRolesAction]
    G -->|last owner?| H{CannotRemoveLastOwnerException}
    G -->|ok| I[(model_has_roles)]

    J[syncPermissions / assignRole] -.busts.-> K[Spatie permission cache]
    K -.reads.-> L[canAccess on every resource]
```

## Related

- [[_module]] · [[data-model]] · [[api]] · [[security]]
- [[../../../architecture/patterns/policy]] · [[../../../architecture/caching]]
