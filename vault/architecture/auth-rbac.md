---
type: architecture
category: auth
color: "#A78BFA"
---

# Authentication and RBAC

Two-layer access control: FlowFlex platform staff (Layer 1) and company users (Layer 2). Separate database tables, separate Laravel guards, separate Filament panels.

---

## Two-Layer Architecture

### Layer 1 — `admins` table (FlowFlex staff)

- Separate `Admin` Eloquent model, `admin` guard
- Login at `/admin` Filament panel only — not accessible to company users
- No `CompanyScope` — can view all companies' data
- Can impersonate any company user (with audit log entry)
- Manages module pricing, company creation, billing

### Layer 2 — `users` table (company users)

- Scoped to `company_id`
- `web` guard — login routes to `/app` workspace panel
- Roles scoped to their company via Spatie Permission teams
- Company owner role assigned at company creation — cannot be removed
- Permissions format: `domain.module.action`

---

## Permission Format

```
hr.employees.view-any
hr.employees.view
hr.employees.create
hr.employees.update
hr.employees.delete
finance.invoices.view-any
finance.invoices.approve
crm.contacts.view-any
```

---

## Built-In Roles

| Role | Scope | Description |
|---|---|---|
| `owner` | Company | All permissions. Assigned at creation. Cannot be removed. |
| `admin` | Company | All permissions except company deletion and owner management. |
| `manager` | Company | View-any and update within assigned modules. No delete. |
| `employee` | Company | View-only within active modules. |

Companies can create additional custom roles with any permission combination.

---

## Guards

```php
// All domain panels and /app workspace
->authGuard('web')
->authModel(User::class)

// /admin panel (FlowFlex staff only)
->authGuard('admin')
->authModel(Admin::class)

// REST API
'middleware' => ['auth:sanctum']
```

---

## Panel-Level Access

Admin panel restricts to `Admin` model:
```php
->authGuard('admin')->authModel(Admin::class)
```

Domain panels restrict to users with at least one domain permission:
```php
->authGuard('web')
->authModel(User::class)
->auth(fn (User $user) => $user->can('access.hr-panel'))
```

`access.hr-panel` is granted to any user whose role includes at least one HR permission.

---

## Spatie Permission Teams

Roles and permissions are scoped to `team_id = company_id`. Must call `setPermissionsTeamId($company->id)` on every request and in every queue job before any permission check.

See [[architecture/multi-tenancy]] — `SetCompanyContext` middleware and `WithCompanyContext` job middleware handle this.

---

## API Authentication (Sanctum)

```
POST /api/v1/auth/token
Body: { email, password, device_name }
Response: { token: "xxx" }

GET /api/v1/employees
Header: Authorization: Bearer xxx
```

Tokens carry abilities (scopes). Rate limiting per endpoint. Defined in `AuthController`.

---

## Company Owner Bootstrap

On company creation:
```php
$role = Role::create(['name' => 'owner', 'team_id' => $company->id]);
$role->syncPermissions(Permission::all());
$owner->assignRole($role);
setPermissionsTeamId($company->id);
```

Runs inside `CompanyCreationService` — called from the admin panel when FlowFlex staff create a new tenant company.
