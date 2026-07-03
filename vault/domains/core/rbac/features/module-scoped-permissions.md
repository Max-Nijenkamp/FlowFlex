---
domain: core
module: rbac
feature: module-scoped-permissions
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Module-Scoped Role Permissions

## Behaviour

The owner (and any role holding `core.rbac.*`) builds roles freely, but the **assignable permission set is
bounded to the company's currently-active modules**.

- Assignable permissions = permissions of `core.*` (always active) ∪ permissions of every **active** paid
  module ([[../../../../infrastructure/module-catalog]] ∩ `company_module_subscription`).
- Permissions of inactive modules are **not offered** in the role builder.
- **Deactivating a module** removes its permissions from the assignable set and **suspends** them on existing
  roles (kept but inert) until the module is reactivated — no hard delete, so re-activation restores role
  intent.
- The `owner` role is exempt (always holds everything for active modules; inactive-module permissions are
  simply inert for everyone).
- Enforced server-side in `CreateRoleAction` / `AssignRolesAction` (reject a permission whose module is
  inactive), not just hidden in the UI — defence in depth ([[../../../../security/authn-authz]]).

## UI

- **Kind**: custom-page (the role builder inside `RoleResource` create/edit).
- **Page**: "Create / edit role" (`/app/roles/create`).
- **Layout**: permission matrix grouped by domain → module; **only active-module groups render**; inactive
  modules shown as a greyed "activate in marketplace to grant" hint row.
- **Key interactions**: toggle permission checkboxes per module group; "select all in module"; save →
  server re-validates against active modules.
- **States**: default (active groups) · empty (only core groups if no paid modules) · error (attempted
  inactive-module permission → inline reject) · saved.
- **Gating**: `core.rbac.create` / `core.rbac.update`.

## Data

- Writes: Spatie role↔permission pivots (own tables). Reads: active-module set. No cross-domain writes
  ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `ModuleActivated` → widen assignable set; `ModuleDeactivated` → suspend that module's grants.
- Feeds: the effective permissions every `canAccess()` reads.

## Test Checklist

### Unit
- [ ] Assignable set = `core.*` ∪ active paid-module permissions; inactive-module permissions excluded
- [ ] `owner` role is exempt from the module-scoped bound

### Feature (Pest)
- [ ] Granting an inactive-module permission is rejected server-side in `CreateRoleAction`/`AssignRolesAction`
- [ ] `ModuleDeactivated` suspends (does not delete) that module's grants on existing roles; `ModuleActivated` restores them
- [ ] Effective `canAccess()` for a user denies a suspended permission until reactivation

### Livewire
- [ ] Role builder renders only active-module groups; inactive modules show the "activate in marketplace" hint row

## Related

- [[../_module|RBAC]] · [[custom-roles]] · [[ownership]] · [[../../module-marketplace/_module]] · [[../../../../infrastructure/module-catalog]]
