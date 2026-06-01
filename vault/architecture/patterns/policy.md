---
type: architecture
category: pattern
color: "#A78BFA"
---

# Authorization: Spatie Permission, Not Laravel Policies

FlowFlex uses `spatie/laravel-permission` with team scoping for all authorization. **Laravel Policies are not used.** This is a deliberate decision.

---

## Why Not Laravel Policies

Laravel Policies are per-model authorization classes. They work well for simple apps with fixed roles. They break down for FlowFlex because:

1. **Dynamic roles**: companies create custom roles with any permission combination. Policies hardcode role names.
2. **Team scoping**: Spatie Permission's team feature scopes permissions per `company_id`. Policies don't understand teams.
3. **Filament integration**: Filament 5's `canAccess()` + `can()` methods integrate directly with Spatie Permission — no Policy registration needed.
4. **Module gating**: authorization in FlowFlex always has two conditions (permission + module subscription). Policies handle only the permission check.

---

## The Authorization Stack

```
canAccess() check
    └── Auth::user()->can('hr.employees.view-any')     ← Spatie Permission check
    └── BillingService::hasModule('hr.employees')      ← Module subscription check
```

Spatie Permission's `can()` is registered as a Laravel Gate check automatically. `Auth::user()->can('hr.employees.view-any')` works as expected.

---

## Permission Naming Convention

```
{domain}.{module}.{action}

Actions: view-any, view, create, update, delete, approve, reject, export, manage
```

Examples:
```
hr.employees.view-any         ← list all employees
hr.employees.view             ← view a single employee record
hr.employees.create
hr.employees.update
hr.employees.delete
hr.leave.approve
hr.payroll.run
finance.invoices.send
finance.invoices.void
crm.deals.view-all            ← special: view deals owned by others (manager scope)
core.rbac.manage-roles
```

`view-any` matches Filament's built-in `canViewAny()` convention — using this name means Filament's authorization hooks work without additional mapping.

---

## Role Assignment in Code

```php
// Assign a role to a user (always within a company team)
setPermissionsTeamId($company->id);
$user->assignRole('manager');

// Check a permission
$user->can('hr.employees.create'); // true/false

// Check in Blade
@can('hr.leave.approve')
    <button>Approve</button>
@endcan
```

---

## Filament Authorization Hooks

Filament resources have built-in authorization hooks. Override them to use Spatie:

```php
public static function canViewAny(): bool
{
    return Auth::user()->can('hr.employees.view-any')
        && BillingService::hasModule('hr.employees');
}

public static function canCreate(): bool
{
    return Auth::user()->can('hr.employees.create')
        && BillingService::hasModule('hr.employees');
}

public static function canEdit(Model $record): bool
{
    return Auth::user()->can('hr.employees.update');
}

public static function canDelete(Model $record): bool
{
    return Auth::user()->can('hr.employees.delete');
}
```

`canAccess()` controls whether the resource appears in the sidebar. The individual `can*()` methods control whether buttons and forms appear.

---

## Super-Admin Bypass

FlowFlex admin staff (the `Admin` model, not company users) bypass all permission checks. The `/admin` panel uses the `admin` guard — `Auth::user()` returns an `Admin`, not a `User`. No Spatie Permission checks apply.

For the owner role within a company (company user with all permissions), standard Spatie Permission applies — owner has all permissions for their company's team.
