---
tags: [flowflex, architecture, modular-monolith, inertia, vue, dtos, services, phase/1]
domain: Platform
status: built
last_updated: 2026-05-08
---

# Architecture

FlowFlex is a **modular monolith** built on Laravel 13. Two frontend stacks coexist: Inertia.js + Vue 3 for the application and public-facing pages, Filament 5 for admin and domain panels.

---

## Application Stack Overview

```
┌─────────────────────────────────────────────────────────┐
│  PUBLIC / TENANT APP (Inertia + Vue 3 + TypeScript)      │
│  Marketing site · Client portal · Community · Booking    │
│  Public checkout · Learner portal · Public org chart     │
├─────────────────────────────────────────────────────────┤
│  DOMAIN PANELS (Filament 5 — TALL stack)                 │
│  HR · Finance · Projects · CRM · Marketing · Ops ·       │
│  Analytics · IT · Legal · Ecommerce · Comms · LMS ·      │
│  AI · Community (admin side)                             │
├─────────────────────────────────────────────────────────┤
│  PLATFORM PANELS (Filament 5)                            │
│  Super-admin (FlowFlex staff) · Workspace (company admin)│
├─────────────────────────────────────────────────────────┤
│  LARAVEL 13 BACKEND                                      │
│  Modular Monolith · PostgreSQL · Redis · Laravel Queues  │
└─────────────────────────────────────────────────────────┘
```

---

## Modular Structure

```
app/
  Modules/
    Core/
    HR/
    Finance/
    Projects/
    CRM/
    Marketing/
    Operations/
    Analytics/
    IT/
    Legal/
    Ecommerce/
    Communications/
    Learning/
    AI/
    Community/
```

### Every Module Has This Exact Structure

```
Modules/HR/
  Contracts/
    HRServiceInterface.php          ← interface definitions
    HRRecruiting ServiceInterface.php
  Services/
    HRService.php                   ← implements HRServiceInterface
    HRRecruitingService.php
  Providers/
    HRServiceProvider.php           ← binds Interface → Service
  Models/
  DTOs/
    CreateEmployeeData.php          ← spatie/laravel-data DTOs
    UpdateEmployeeData.php
    EmployeeResource.php            ← response DTOs
  Http/
    Controllers/
      EmployeeController.php        ← only receives Interface, thin
    Middleware/
  Filament/
    Resources/
    Pages/
    Widgets/
  Events/
  Listeners/
  Policies/
  database/
    migrations/
    seeders/
    factories/
  config/
    hr.php
  routes/
    web.php                         ← Inertia routes (if module has Vue pages)
    api.php
  resources/
    js/
      pages/                        ← Vue page components for this module
      components/
```

---

## Service Layer Pattern

**The rule:** Controllers are dumb. All business logic lives in Services. Controllers only receive the interface via dependency injection.

### 1 — Define the Interface

```php
// Modules/HR/Contracts/EmployeeServiceInterface.php
namespace App\Modules\HR\Contracts;

use App\Modules\HR\DTOs\CreateEmployeeData;
use App\Modules\HR\DTOs\UpdateEmployeeData;
use App\Modules\HR\DTOs\EmployeeResource;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeServiceInterface
{
    public function list(int $perPage = 25): LengthAwarePaginator;
    public function find(string $id): EmployeeResource;
    public function create(CreateEmployeeData $data): EmployeeResource;
    public function update(string $id, UpdateEmployeeData $data): EmployeeResource;
    public function delete(string $id): void;
}
```

### 2 — Implement the Service

```php
// Modules/HR/Services/EmployeeService.php
namespace App\Modules\HR\Services;

use App\Modules\HR\Contracts\EmployeeServiceInterface;
use App\Modules\HR\DTOs\CreateEmployeeData;
use App\Modules\HR\DTOs\EmployeeResource;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Events\EmployeeProfileCreated;

class EmployeeService implements EmployeeServiceInterface
{
    public function create(CreateEmployeeData $data): EmployeeResource
    {
        $employee = Employee::create($data->toArray());
        event(new EmployeeProfileCreated($employee));
        return EmployeeResource::fromModel($employee);
    }

    // ... other methods
}
```

### 3 — Bind in ServiceProvider

```php
// Modules/HR/Providers/HRServiceProvider.php
namespace App\Modules\HR\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\HR\Contracts\EmployeeServiceInterface;
use App\Modules\HR\Services\EmployeeService;

class HRServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
```

### 4 — Thin Controller

```php
// Modules/HR/Http/Controllers/EmployeeController.php
namespace App\Modules\HR\Http\Controllers;

use App\Modules\HR\Contracts\EmployeeServiceInterface;
use App\Modules\HR\DTOs\CreateEmployeeData;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeServiceInterface $employees,
    ) {}

    public function index(): Response
    {
        return Inertia::render('HR/Employees/Index', [
            'employees' => $this->employees->list(),
        ]);
    }

    public function store(CreateEmployeeData $request): JsonResponse
    {
        $employee = $this->employees->create($request);
        return response()->json($employee, 201);
    }
}
```

---

## DTO Pattern (spatie/laravel-data)

All data crossing layer boundaries uses typed DTOs. No raw arrays, no FormRequest objects, no untyped $request->validated().

### Input DTO (replaces FormRequest)

```php
// Modules/HR/DTOs/CreateEmployeeData.php
namespace App\Modules\HR\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;

class CreateEmployeeData extends Data
{
    public function __construct(
        #[Required, StringType, Max(100)]
        public readonly string $first_name,

        #[Required, StringType, Max(100)]
        public readonly string $last_name,

        #[Required, Email]
        public readonly string $email,

        #[Required]
        public readonly string $department_id,

        public readonly ?string $job_title = null,
        public readonly ?string $phone = null,
        public readonly ?\DateTimeImmutable $start_date = null,
    ) {}
}
```

### Response DTO (replaces JsonResource)

```php
// Modules/HR/DTOs/EmployeeResource.php
namespace App\Modules\HR\DTOs;

use Spatie\LaravelData\Data;
use App\Modules\HR\Models\Employee;

class EmployeeResource extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $full_name,
        public readonly string $email,
        public readonly string $department,
        public readonly string $job_title,
        public readonly string $status,
        public readonly ?string $avatar_url,
        public readonly string $created_at,
    ) {}

    public static function fromModel(Employee $employee): self
    {
        return new self(
            id: $employee->id,
            full_name: $employee->full_name,
            email: $employee->email,
            department: $employee->department->name,
            job_title: $employee->job_title ?? '',
            status: $employee->status->value,
            avatar_url: $employee->avatarUrl(),
            created_at: $employee->created_at->toISOString(),
        );
    }
}
```

### DTO Rules

- Input DTOs: hold validation rules via attributes, never extend FormRequest
- Response DTOs: shape the API response, never expose internal fields (e.g. `password`, `company_id`)
- Service input/output: all public service methods accept and return DTOs
- Never pass Eloquent models across module boundaries — convert to DTOs at the service boundary

---

## Cross-Module Communication

**Rule: modules never import each other's internal classes directly.**

### Allowed

1. **Laravel Events** — fire events; other modules listen
2. **Core shared models** — `User`, `Company`, `File` live in Core, any module can use them
3. **Registered contracts** — if Module A needs data from Module B, consume via interface registered in Core

```php
// CORRECT: HR fires, Finance listens independently
event(new TimeEntryApproved($entry));

// WRONG: HR directly calls Finance service
// ❌ app(PayrollServiceInterface::class)->addEntry($entry);
```

### Service Contract Example (Cross-Module)

When Finance needs employee data from HR:

```php
// Core/Contracts/EmployeeRepositoryInterface.php — registered in Core
interface EmployeeRepositoryInterface {
    public function findById(string $id): EmployeeDTO;
}

// HR implements it, registers binding in HRServiceProvider
// Finance injects EmployeeRepositoryInterface — never App\Modules\HR\Services\...
```

---

## Inertia.js + Vue 3 Pattern

The application uses Inertia.js for all non-Filament pages.

### Route Definition

```php
// routes/web.php
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/hr/employees', [EmployeeController::class, 'index'])
        ->name('hr.employees.index');
});
```

### Controller Returns Inertia Response

```php
return Inertia::render('HR/Employees/Index', [
    'employees' => EmployeeResource::collect($this->employees->list()),
    'departments' => DepartmentResource::collect($departments),
]);
```

### Vue Page Component

```vue
<!-- resources/js/pages/HR/Employees/Index.vue -->
<script setup lang="ts">
import { EmployeeResource } from '@/types/hr'
import { Link, router } from '@inertiajs/vue3'

interface Props {
  employees: {
    data: EmployeeResource[]
    meta: { total: number; current_page: number }
  }
}

const props = defineProps<Props>()
</script>

<template>
  <AppLayout title="Employees">
    <!-- component markup -->
  </AppLayout>
</template>
```

### TypeScript Types

Generate TypeScript types from DTOs automatically via `spatie/laravel-typescript-transformer`:

```bash
php artisan typescript:transform
```

This generates `resources/js/types/generated.d.ts` from all `Data` classes — controllers and Vue components share the same types.

---

## Module Registry & Billing

```
companies
  └── company_modules (which modules are active per company)
       └── module_usage_events (metered usage → Stripe)
```

- When a module is toggled off → Filament panel hidden, Inertia routes disabled, data preserved
- Module activation fires `ModuleActivated` event → all module components activate
- Usage metering: events recorded → Stripe sync via scheduled job

---

## Module Dependency Rules

```
Core Platform (always active)
├── All modules inherit: Auth, RBAC, Tenancy, Notifications, Files, API

HR Domain
├── Employee Profiles (standalone)
├── Onboarding → requires Employee Profiles
├── Payroll → requires Employee Profiles
│   └── benefits from: Time Tracking, Leave, Expenses
├── Recruitment → standalone; on hire → Employee Profiles + Onboarding
└── [see full map in Cross-Module Event Map]

Finance Domain
├── Invoicing (standalone)
├── Bank Reconciliation → benefits from: Invoicing
├── Multi-Currency → extends all Finance modules
├── Open Banking → requires Bank Reconciliation
└── Cash Flow Forecasting → requires: Invoicing, AP/AR, Open Banking

[full dependency map in Cross-Module Event Map]
```

---

## Why Modular Monolith

- Single deployment, single log stream, easy to debug
- No distributed systems complexity during early phases
- Each module is isolated enough to extract to a microservice when scale demands
- Shared infrastructure: one DB, one cache, one queue
- Module-level feature flags and billing without service mesh

---

## Related

- [[Tech Stack]]
- [[Multi-Tenancy]]
- [[Roles & Permissions (RBAC)]]
- [[Error Handling]]
- [[Rate Limiting]]
- [[Naming Conventions]]
- [[Security Rules]]
- [[Performance Rules]]
- [[Module Development Checklist]]
