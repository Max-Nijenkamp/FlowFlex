---
type: architecture
category: security
last_updated: 2026-05-08
---

# Authentication & RBAC

FlowFlex uses a **2-layer RBAC** model: one for platform super-admins, one for company users.

---

## Two-Layer Architecture

```mermaid
graph TD
    subgraph Layer1["Layer 1 — FlowFlex Platform (admins table)"]
        SA["Super Admin\n(FlowFlex staff)"]
        SA -->|"manages"| Tenants["All Tenant Companies"]
        SA -->|"can impersonate"| Any["Any User"]
    end

    subgraph Layer2["Layer 2 — Company Workspace (users table)"]
        CO["Company Owner\n(all permissions)"]
        CR["Custom Roles\n(Spatie Permission)"]
        TM["Team Member\n(inherits role)"]

        CO -->|"creates"| CR
        CR -->|"assigned to"| TM
    end

    Tenants --> Layer2
```

### Layer 1 — `admins` Table

- Separate table from company users
- Login at `/admin` Filament panel
- Can access all companies for support
- Can impersonate any user with full audit log

### Layer 2 — `users` Table

- Scoped to `company_id`
- Company owner gets all permissions at registration
- All other users assigned one or more custom roles
- Roles are per-company (Spatie's `team` concept)
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

Wildcards for panel-level access:
```
hr.*              → all HR permissions
finance.invoices.*  → all invoice permissions
```

---

## Authentication Flows

### Web (SPA via Inertia)

```mermaid
sequenceDiagram
    participant Browser
    participant Laravel
    participant Session
    participant DB

    Browser->>Laravel: POST /login {email, password}
    Laravel->>DB: find user by email
    DB-->>Laravel: User record
    Laravel->>Laravel: Hash::check(password)
    Laravel->>Session: regenerate session
    Laravel-->>Browser: 302 redirect to dashboard
    Note over Browser,Laravel: Subsequent requests include session cookie
    Browser->>Laravel: GET /hr/employees
    Laravel->>Session: verify auth
    Laravel-->>Browser: Inertia response
```

### API (Token via Sanctum)

```mermaid
sequenceDiagram
    participant Client as API Client
    participant Laravel

    Client->>Laravel: POST /api/v1/auth/token {email, password, device_name}
    Laravel-->>Client: {token: "xxx"}
    Client->>Laravel: GET /api/v1/employees\nAuthorization: Bearer xxx
    Laravel->>Laravel: auth:sanctum middleware
    Laravel-->>Client: JSON response
```

---

## Company Owner Bootstrap

When a new company is created:

```php
// In TenantRegistrationService
$owner = User::create([...]);
$role = Role::create(['name' => 'owner', 'team_id' => $company->id]);
$allPermissions = Permission::all();
$role->syncPermissions($allPermissions);
$owner->assignRole($role);
```

---

## Middleware Stack

```php
Route::middleware([
    'auth',
    'verified',
    'company.scope',     // sets company context, blocks suspended tenants
])->group(function () {
    // all domain routes
});
```

`company.scope` middleware:
1. Resolves company from user's `company_id`
2. Sets app-level company context for global scopes
3. Checks company subscription status (blocks if suspended)
4. Injects `current_company` into Inertia shared data

---

## Filament Panel Auth

Each Filament panel has its own auth provider:

```php
// Admin panel (FlowFlex staff only)
->authGuard('admin')
->authModel(Admin::class)

// Domain panels (company users with panel permission)
->authGuard('web')
->authModel(User::class)
->auth(fn (User $user) => $user->can('access.hr-panel'))
```

---

## Related

- [[MOC_Architecture]]
- [[multi-tenancy]]
- [[concept-rbac]]
- [[entity-user]]
