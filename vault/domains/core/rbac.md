---
type: module
domain: Core Platform
panel: app
module-key: core.rbac
status: planned
color: "#4ADE80"
---

# Roles & Permissions

Role management UI for company admins. Create custom roles, assign permissions per module, manage user role assignments. Backed by `spatie/laravel-permission` with `bezhansalleh/filament-shield` for the Filament UI.

---

## Core Features

- Built-in roles: `owner`, `admin`, `manager`, `employee` (see [[architecture/auth-rbac]])
- Custom role creation with any permission combination
- Permission assignment UI per role — permissions grouped by domain
- User role assignment — one or more roles per user
- Permission format: `domain.module.action` (e.g. `hr.employees.create`)
- Panel-level access permissions: `access.hr-panel`, `access.finance-panel`, etc.
- `filament-shield` generates permission UI from discovered Filament resources
- Role changes take effect immediately (no cache — Spatie Permission queries live)
- Company owner role cannot be removed

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

## Filament

**`/app` panel:**
- `RoleResource` — list, create, edit roles; assign permissions per role
- `UserResource` — list users, assign roles, invite new users
- `InvitationResource` — pending invitations management

---

## Related

- [[architecture/auth-rbac]]
- [[domains/core/company-settings]]
