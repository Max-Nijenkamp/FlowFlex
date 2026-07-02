---
domain: core
module: rbac
feature: custom-roles
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# RBAC — Custom Roles

Parent: [[../_module]]

Company admins create roles beyond the four built-ins (`owner`, `admin`, `manager`, `employee`) with any permission combination.

## Creation flow

1. Admin opens `RoleResource` in the `/app` Team nav.
2. Names the role and ticks permissions on the matrix.
3. `CreateRoleAction` (see [[../architecture]]) persists it under `team_id = company_id` and `syncPermissions()`.
4. The new role is immediately assignable to users via `UserResource` / [[../api|AssignRolesData]].

## Permission matrix

- Permissions render **grouped by domain** in the form (all `hr.*` together, all `finance.*` together, etc.), so admins reason per business area rather than scanning a flat list.
- Permission strings follow `domain.module.action`; panel access appears as `access.{domain}-panel`.

## filament-shield generation

`bezhansalleh/filament-shield` discovers Filament resources across all panels and **generates the permission matrix** from them — new resources surface as new checkboxes without hand-authoring permission strings. Shield also seeds the built-in roles' permission sets.

## UI

- **Kind**: custom-page (role builder inside `RoleResource` create/edit).
- **Page**: "Create / edit role" (`/app/roles/create`, `/app/roles/{id}/edit`).
- **Layout**: role name + description; permission matrix grouped by domain → module. **Only active-module
  groups render** ([[module-scoped-permissions]]); shield-generated checkboxes.
- **Key interactions**: toggle permissions per module group; "select all in module"; save → server
  re-validates every permission against the company's active modules.
- **States**: default (active groups) · empty (only `core.*` if no paid modules) · error (inactive-module
  or built-in-role edit → inline reject) · saved.
- **Gating**: `core.rbac.create` / `core.rbac.update`.

## Data

- Writes: Spatie role + role↔permission pivots (`team_id = company_id`). Reads: active-module set. No
  cross-domain writes ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `ModuleActivated` / `ModuleDeactivated` (core.billing) → widen/narrow the assignable permission set.
- Feeds: the role definitions every module's `canAccess()` reads.

## Related

- [[../_module]] · [[../architecture]] · [[../security]] · [[../api]]
- [[last-owner-guard]] · [[ownership]] · [[module-scoped-permissions]]
