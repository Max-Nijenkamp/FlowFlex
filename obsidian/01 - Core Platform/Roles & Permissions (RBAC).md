---
tags: [flowflex, core, rbac, permissions, roles, spatie, phase/1]
domain: Core Platform
panel: workspace
color: "#2199C8"
status: built
last_updated: 2026-05-08
---

# Roles & Permissions (RBAC)

Two-layer access control. FlowFlex super-admins operate at the platform level. Company owners and admins control access within their own organisation, down to individual module features.

---

## Two RBAC Layers

```
Layer 1: FlowFlex Platform (super-admin panel)
  └── FlowFlex staff with access to all tenants, billing, platform settings

Layer 2: Company / Tenant (workspace + domain panels)
  └── Company Owner → creates roles → assigns to tenants
       └── Roles have permissions: which modules, which actions, which records
```

These layers are completely separate. A FlowFlex super-admin has no business permissions within a tenant. A company owner has no platform access.

---

## Layer 1: FlowFlex Super-Admin

### Who Has It

- FlowFlex employees with explicit `super_admin` flag
- Stored in the `admins` table, NOT the `tenants` table
- Separate login: `admin.flowflex.com/admin`
- Separate Filament panel: `App\Providers\Filament\AdminPanelProvider`

### What They Can Do

- View all companies (read-only by default)
- Impersonate a company to investigate support issues (every impersonation is logged)
- Manage subscription plans and billing
- Feature flag management
- Platform-level audit log
- Module version management

### Super-Admin Implementation

```php
// app/Models/Admin.php — separate model, not in tenant scope
class Admin extends Authenticatable
{
    use HasUlids;

    // NOT BelongsToCompany — platform level
    protected $fillable = ['name', 'email', 'password'];

    public function isSuperAdmin(): bool
    {
        return true; // all Admins are super-admins
    }
}
```

```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        ->domain(config('app.central_domain'))  // flowflex.com
        ->authGuard('admin')
        ->login()
        ->colors(['primary' => Color::hex('#0D2D3F')])
        ->brandName('FlowFlex Admin');
}
```

---

## Layer 2: Company RBAC

### Roles

Every company has these roles out of the box. None can be deleted.

| Role | Description |
|---|---|
| `owner` | Created when company is founded. Full access, billing access. Only one. |
| `admin` | Full access to all active modules. Cannot manage billing or transfer ownership. |
| `member` | Base role. No module access until a role with permissions is assigned. |
| `read_only` | View-only across all modules the user can access. |

Custom roles can be created by the Owner or Admin.

### Company Setup Flow (Owner)

When a new company is created, the founding user becomes the Owner and is walked through:

1. **Activate modules** — toggle on the modules the company needs
2. **Create roles** — e.g. "HR Manager", "Finance Viewer", "Sales Rep"
3. **Assign permissions to roles** — granular permission grid
4. **Invite team members** — email invite → new tenant assigned a role on acceptance

All of this is self-serve via the Workspace panel. No FlowFlex consultant required.

---

## Permission Structure

All permissions follow: `{module}.{resource}.{action}`

### Panel Access

```
hr.panel.access
finance.panel.access
crm.panel.access
projects.panel.access
operations.panel.access
analytics.panel.access
it.panel.access
legal.panel.access
ecommerce.panel.access
communications.panel.access
lms.panel.access
ai.panel.access
community.panel.access
```

### Resource-Level (examples)

```
hr.employees.view
hr.employees.create
hr.employees.edit
hr.employees.delete
hr.employees.export

finance.invoices.view
finance.invoices.create
finance.invoices.send
finance.invoices.void
finance.payroll.view
finance.payroll.run

crm.deals.view
crm.deals.create
crm.deals.edit
crm.deals.delete
crm.deals.view-all       ← see all deals, not just own
crm.deals.export
```

### Field-Level (sensitive data)

```
hr.employees.salary.view
hr.employees.salary.edit
hr.employees.national_id.view
hr.employees.bank_details.view
finance.invoices.unit_cost.view
```

### Scoped Permissions

```
hr.employees.department:{department_id}   ← see only own department
crm.deals.team:{team_id}                  ← see only own team's deals
analytics.reports.company-wide            ← vs department-only
```

---

## Role Builder UI (Workspace Panel)

### Permission Matrix

Grid view: **permissions (rows) grouped by module** vs **roles (columns)**, with toggle cells.

```
Module: HR & People
                         HR Manager   Recruiter   HR Viewer
hr.employees.view            ✅           ✅           ✅
hr.employees.create          ✅           ❌           ❌
hr.employees.edit            ✅           ❌           ❌
hr.employees.delete          ❌           ❌           ❌
hr.employees.salary.view     ✅           ❌           ❌
hr.recruitment.view          ✅           ✅           ❌
hr.recruitment.create        ✅           ✅           ❌
...

Module: Finance & Accounting
finance.invoices.view        ✅           ❌           ✅
...
```

### Role Features

- Create role: name, description, colour (for display), clone from existing
- Role inheritance: a Senior Manager role can extend a Manager role (inherits all permissions)
- Role assignment: assign to individual users or bulk-assign to a team
- Effective permissions view: for any specific user, see their full resolved permissions set (including all role-inherited permissions)
- Temporary role grants: assign with auto-expiry date (e.g. cover for maternity leave)

---

## Implementation in Filament Resources

Every Filament resource **must** implement all `can*` methods explicitly. Never rely on defaults.

```php
// Example for any HR resource
public static function canViewAny(): bool
{
    return auth()->user()->can('hr.employees.view');
}

public static function canCreate(): bool
{
    return auth()->user()->can('hr.employees.create');
}

public static function canEdit(Model $record): bool
{
    return auth()->user()->can('hr.employees.edit');
}

public static function canDelete(Model $record): bool
{
    return auth()->user()->can('hr.employees.delete');
}

public static function canDeleteAny(): bool
{
    return auth()->user()->can('hr.employees.delete');
}

public static function canForceDelete(Model $record): bool
{
    return false; // soft delete only
}
```

### Field-Level Visibility

```php
Forms\Components\TextInput::make('salary')
    ->visible(fn () => auth()->user()->can('hr.employees.salary.view'))
    ->disabled(fn () => ! auth()->user()->can('hr.employees.salary.edit')),
```

### Policy Classes

Every model has a Policy class that delegates to permission checks:

```php
// app/Modules/HR/Policies/EmployeePolicy.php
class EmployeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('hr.employees.view');
    }

    public function view(User $user, Employee $employee): bool
    {
        // Record-level: managers can only view their dept
        if ($user->can('hr.employees.view')) {
            if ($user->hasPermissionTo('hr.employees.department:' . $user->department_id)) {
                return $employee->department_id === $user->department_id;
            }
            return true; // company-wide view
        }
        return false;
    }
}
```

---

## Permission Seeding

On company creation, seed all permission definitions:

```php
// database/seeders/PermissionSeeder.php
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = config('permissions.all'); // central list in config/permissions.php

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create system roles
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        // admin gets all except billing permissions
    }
}
```

---

## Database Tables

Spatie standard tables + FlowFlex additions:

| Table | Purpose |
|---|---|
| `permissions` | All permission definitions (seeded, company-scoped via Spatie team) |
| `roles` | Role definitions per company |
| `model_has_roles` | User → Role assignments |
| `model_has_permissions` | Direct user → permission assignments (rare) |
| `role_has_permissions` | Role → Permissions pivot |
| `role_grants` | Temporary role grants with expiry |

### Spatie Team Scope (Multi-Tenancy)

Enable team scopes to isolate roles per company:

```php
// config/permission.php
'teams' => true,
'team_model' => App\Models\Company::class,

// In all permission checks — automatically scoped to current company
setPermissionsTeamId($user->company_id);
```

---

## Module Access Check Middleware

```php
// app/Http/Middleware/ModuleActiveForTenant.php
class ModuleActiveForTenant
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $company = $request->user()->company;

        if (! $company->hasModuleActive($module)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "The {$module} module is not active for your organisation.",
                    'activate_url' => route('workspace.modules.index'),
                ], 403);
            }
            return Inertia::render('Errors/ModuleNotActive', [
                'module' => $module,
            ])->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}

// Register as route middleware alias
// bootstrap/app.php:
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'module' => ModuleActiveForTenant::class,
        'tenant' => EnsureValidTenantScope::class,
    ]);
})

// Usage on routes:
Route::middleware(['auth', 'module:hr'])->group(function () {
    // All HR routes — automatically 403 if module not active
});
```

---

## Events Fired

| Event | Payload | Logged |
|---|---|---|
| `RoleCreated` | `role_id`, `created_by` | Audit log |
| `RoleUpdated` | `role_id`, `changes`, `updated_by` | Audit log |
| `PermissionGranted` | `user_id`, `permission`, `granted_by` | Audit log |
| `PermissionRevoked` | `user_id`, `permission`, `revoked_by` | Audit log |
| `OwnershipTransferred` | `from_user_id`, `to_user_id` | Audit log + email |
| `ImpersonationStarted` | `admin_id`, `user_id` | Super-admin audit log |

---

## Related

- [[Authentication & Identity]]
- [[Multi-Tenancy & Workspace]]
- [[Security Rules]]
- [[Architecture]]
- [[Module Development Checklist]]
