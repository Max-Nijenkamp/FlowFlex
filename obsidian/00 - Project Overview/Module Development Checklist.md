---
tags: [flowflex, checklist, module-development, phase/1]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Module Development Checklist

Follow this checklist **in order** when building any new module. Do not skip steps.

## The Checklist

- [ ] Create `Modules/{ModuleName}/Providers/{ModuleName}ServiceProvider.php` and register in `config/app.php`
- [ ] Create `config/{module_name}.php` with module metadata (name, version, dependencies)
- [ ] Add module to `modules` registry table via seeder
- [ ] Create all database migrations under `Modules/{ModuleName}/database/migrations/`
- [ ] Every table must have `tenant_id`, timestamps, and soft deletes
- [ ] Create Eloquent models with `BelongsToTenant` trait applied
- [ ] Register all Spatie permissions for this module: `{module}.{resource}.{action}`
- [ ] Create Filament Panel (or add resources to existing panel)
- [ ] Create Filament Resources with proper auth policies
- [ ] Register all Events and Listeners in the ServiceProvider
- [ ] Expose API routes in `Modules/{ModuleName}/routes/api.php`
- [ ] Write Feature tests for all key flows
- [ ] Document all events fired and all events listened to in `Modules/{ModuleName}/README.md`

## Step-by-Step Detail

### Step 1: Service Provider

```php
// Modules/HR/Providers/HRServiceProvider.php
namespace App\Modules\HR\Providers;

use Illuminate\Support\ServiceProvider;

class HRServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // bind interfaces to implementations
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->mergeConfigFrom(__DIR__ . '/../../config/hr.php', 'hr');

        // Register events
        Event::listen(CandidateHired::class, CreateEmployeeProfile::class);
        Event::listen(CandidateHired::class, StartOnboardingFlow::class);
    }
}
```

Register in `config/app.php` providers array.

### Step 2: Module Config

```php
// config/hr.php
return [
    'name' => 'HR & People',
    'version' => '1.0.0',
    'module_key' => 'hr',
    'panel_id' => 'hr',
    'domain_colour' => '#7C3AED',
    'dependencies' => [], // module keys that must be active
    'permissions' => [
        'hr.panel.access',
        'hr.employees.view',
        'hr.employees.create',
        'hr.employees.edit',
        'hr.employees.delete',
        // ... all permissions
    ],
];
```

### Step 3: Database Tables

Every table in the module must have:

```php
Schema::create('employees', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->ulid('tenant_id');
    $table->string('first_name');
    $table->string('last_name');
    // ... other fields ...
    $table->timestamps();
    $table->softDeletes();

    // Required indexes
    $table->index('tenant_id');
    $table->index('status');
    $table->index(['tenant_id', 'status']); // composite for common filters
    $table->foreign('tenant_id')->references('id')->on('tenants');
});
```

### Step 4: Eloquent Models

```php
namespace App\Modules\HR\Models;

use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use BelongsToTenant;
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;

    protected $casts = [
        'salary' => 'encrypted:integer',
        // ...
    ];
}
```

### Step 5: Filament Resource with RBAC

```php
class EmployeeResource extends Resource
{
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
}
```

### Step 6: Events and Listeners

```php
// Events must be past-tense class names:
// App\Modules\HR\Events\EmployeeHired

// Listeners must be imperative action class names:
// App\Modules\Onboarding\Listeners\CreateOnboardingTasks

// Register in ServiceProvider:
Event::listen(EmployeeHired::class, [
    CreateOnboardingTasks::class,    // Onboarding module
    AddToPayrollRun::class,          // Payroll module
    AssignInductionCourse::class,    // LMS module
]);
```

### Step 7: API Routes

```php
// Modules/HR/routes/api.php
Route::prefix('v1/hr')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::apiResource('employees', EmployeeController::class);
    // ...
});
```

## Build Sizing Reference

| Module | Complexity | Filament Resources | Migrations |
|---|---|---|---|
| Auth & Identity | Medium | 3 resources, 2 pages | 4 tables |
| RBAC | Medium | 2 resources | 3 tables (Spatie) |
| Module Billing | High | 1 resource, 3 pages | 4 tables |
| Employee Profiles | Medium | 1 resource, 1 page | 5 tables |
| Onboarding | High | 3 resources, 2 pages | 6 tables |
| Leave Management | High | 2 resources, 2 pages | 5 tables |
| Payroll | Very High | 4 resources, 3 pages | 10 tables |
| Recruitment (ATS) | Very High | 5 resources, 3 pages | 10 tables |
| Task Management | High | 3 resources, 4 pages | 6 tables |
| Invoicing | Very High | 3 resources, 2 pages | 6 tables |
| Customer Support | Very High | 3 resources, 3 pages | 8 tables |
| Inventory | Very High | 3 resources, 2 pages | 8 tables |
| Field Service | Very High | 4 resources, 3 pages | 9 tables |
| CMS | Very High | 3 resources, 2 pages | 6 tables |
| Email Marketing | Very High | 3 resources, 3 pages | 8 tables |

## Related

- [[Naming Conventions]]
- [[Architecture]]
- [[Security Rules]]
- [[Tech Stack]]
- [[Roles & Permissions (RBAC)]]
