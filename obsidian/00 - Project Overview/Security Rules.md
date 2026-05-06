---
tags: [flowflex, security, rules, phase/1]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Security Rules

Non-negotiable security rules for the FlowFlex platform. Every one of these applies to every module, every endpoint, every piece of code.

## The Rules

### 1. Never Trust User Input

All form data goes through Laravel Form Requests with explicit validation rules.

- Every controller action that accepts user input uses a dedicated `FormRequest` class
- No `$request->all()` mass-assignment without explicit validation
- Validate types, lengths, formats, and enum values explicitly

### 2. All API Endpoints Require Authentication

No public endpoints unless explicitly intended (e.g. booking page, storefront).

- Every API route is behind `auth:sanctum` middleware
- Public endpoints (storefront, booking page) are explicitly marked and reviewed
- No accidental exposure of internal APIs

### 3. All Queries Are Tenant-Scoped

A global scope applies `WHERE tenant_id = :current_tenant_id` to every query. **Never bypass this.**

```php
// WRONG — exposes all tenants' data
Employee::all();

// CORRECT — global scope applies automatically
Employee::all(); // only returns current tenant's employees because of global scope

// NEVER DO THIS in module code (admin only, with justification)
Employee::withoutGlobalScope(TenantScope::class)->all();
```

See [[Multi-Tenancy]] for full details.

### 4. Sensitive Fields Are Encrypted at Rest

Bank details, national insurance numbers, salary data, API keys must use Laravel's `encrypted` cast.

```php
protected $casts = [
    'bank_account_number' => 'encrypted',
    'national_insurance_number' => 'encrypted',
    'salary' => 'encrypted:integer',
    'api_key' => 'encrypted',
];
```

Applies to:
- Bank account details (payroll, expense reimbursements)
- National insurance / social security numbers
- Salary and compensation data
- API keys and secrets
- OAuth tokens
- Any field flagged as sensitive by the business

### 5. Audit Every Write

Use Spatie Activity Log on all models where data changes matter. **This is non-optional.**

```php
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
}
```

Every create, update, delete on any meaningful model is logged with: who did it, when, what changed (before/after values), from which IP.

### 6. File Access Is Signed and Expiring

Never expose raw S3 URLs. Always use signed temporary URLs with expiry.

```php
// WRONG
return Storage::url($file->path); // permanent public URL

// CORRECT
return Storage::temporaryUrl($file->path, now()->addMinutes(15));
```

File permissions (view / edit / download) are checked before generating signed URLs.

### 7. Rate Limit All APIs

Default: 60 requests per minute per API key. Configurable per tenant tier.

```php
// In routes/api.php
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // module routes
});
```

- Starter: 30 req/min
- Pro: 60 req/min
- Enterprise: configurable, up to 1000 req/min

### 8. RBAC on Every Filament Resource

Every `Resource` must implement `canViewAny()`, `canCreate()`, `canEdit()`, `canDelete()` and check the authenticated user's permissions.

```php
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
```

Never rely on Filament's default permission logic — always implement explicitly.

## Permission Naming Convention

All Spatie permissions follow: `{module}.{resource}.{action}`

Examples:
- `hr.employees.view`
- `hr.employees.create`
- `hr.employees.salary.view` (field-level)
- `finance.invoices.delete`
- `hr.panel.access` (panel-level)

## Related

- [[Architecture]]
- [[Multi-Tenancy]]
- [[Roles & Permissions (RBAC)]]
- [[API & Integrations Layer]]
- [[Audit Log & Activity Trail]]
- [[Naming Conventions]]
