---
type: module
domain: Core Platform
panel: app
cssclasses: domain-admin
phase: 1
status: complete
migration_range: 010001–019999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# RBAC Management UI

Filament UI for company owners to create custom roles, assign granular `domain.module.action` permissions, and manage which roles their team members hold. Built entirely on top of `spatie/laravel-permission`, which is bootstrapped and seeded in Phase 0 Foundation.

**Panel:** `app` (company owner only)  
**Phase:** 1 — required before any Phase 2 domain can enforce meaningful access control

---

## Features

### Default Roles (Seeded Per Company)

When a company is created, four default roles are seeded automatically:

| Role | Description |
|------|-------------|
| `owner` | All permissions — cannot be deleted or modified |
| `admin` | Most permissions — can manage team and most domain data |
| `member` | Basic permissions — can use features but not manage settings |
| `viewer` | Read-only — can view records but not create or edit |

The `owner` role is protected: it cannot be deleted, renamed, or have permissions removed via the UI.

### Custom Roles

- Company owner can create additional custom roles with any combination of permissions
- Custom roles can be deleted as long as no users are currently assigned to them
- A role name must be unique within the company (scoped by `team_id = company_id` in spatie/permission)

### Permissions UI

- Permissions are displayed grouped by domain in the UI (HR, Finance, CRM, Inventory, etc.)
- Each group shows a toggle matrix: permission string on the left, role columns across the top
- Toggle state is saved immediately on change (no bulk save required)
- Permissions follow the `{domain}.{module}.{action}` string convention (e.g. `hr.leave.approve`, `finance.invoice.export`)

### PermissionSeeder

- `database/seeders/PermissionSeeder.php` creates all permission strings on first install and after every deploy
- New permissions added in any domain's migration must also be added to `PermissionSeeder`
- Seeder is idempotent — safe to run multiple times (`firstOrCreate`)

### Filament Resources

- `RoleResource` in app panel:
  - List all roles (with user count per role)
  - Create custom role
  - Edit role name and description
  - Delete custom role (guarded — cannot delete `owner` role)
- Permissions sub-page on each role:
  - Toggle grid grouped by domain
  - "Select all in domain" shortcut

### Access Guard

Only users with `core.roles.manage` permission (i.e. the `owner` role) can access `RoleResource`. `canAccess()` checks this permission.

---

## Data Model

Roles and permissions are stored in the spatie/laravel-permission tables (bootstrapped in Phase 0):

```
roles              -- name, guard_name, team_id (= company_id)
permissions        -- name, guard_name
role_has_permissions
model_has_roles    -- user ↔ role
model_has_permissions
```

No additional tables are created by this module.

---

## Permissions

```
core.roles.manage
```

---

## Related

- [[MOC_CorePlatform]]
- [[MOC_Foundation]] — spatie/laravel-permission bootstrapped in Phase 0
- [[entity-user]]
- [[concept-multi-tenancy]]
- [[audit-log]] — role and permission changes are audit-logged
