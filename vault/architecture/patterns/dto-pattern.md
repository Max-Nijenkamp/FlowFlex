---
type: architecture
category: pattern
color: "#A78BFA"
---

# DTO Pattern (spatie/laravel-data)

FlowFlex uses `spatie/laravel-data` Data classes for all input validation and output serialisation. A DTO replaces both Laravel's `FormRequest` and Laravel's `JsonResource` in a single class.

---

## Location

```
app/Data/{Domain}/{Model}Data.php
```

Examples:
- `app/Data/HR/EmployeeData.php`
- `app/Data/HR/CreateEmployeeData.php`
- `app/Data/Finance/InvoiceData.php`
- `app/Data/Projects/TaskData.php`

One DTO per concern. A shared `EmployeeData` for output, and a specific `CreateEmployeeData` for create-only input where the fields differ significantly.

---

## Input DTO

Used for validating request data before passing to the service layer. Constructor attributes carry validation attributes from `spatie/laravel-data`:

```php
namespace App\Data\HR;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreateEmployeeData extends Data
{
    public function __construct(
        #[Required, StringType, Max(100)]
        public readonly string $first_name,

        #[Required, StringType, Max(100)]
        public readonly string $last_name,

        #[Required, Email]
        public readonly string $email,

        #[Required, Date]
        public readonly CarbonImmutable $start_date,

        public readonly ?string $job_title = null,
        public readonly ?string $department_id = null,
    ) {}
}
```

Laravel resolves and validates the DTO from the request automatically when it is type-hinted in a controller method or Livewire action. Invalid input returns `422 Unprocessable Entity` before the controller body runs.

---

## Output DTO

Used for serialising model data for Inertia pages or API responses:

```php
namespace App\Data\HR;

use App\Models\HR\Employee;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class EmployeeData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $company_id,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly CarbonImmutable $start_date,
        public readonly ?string $job_title,
        public readonly string $status,
    ) {}

    public static function fromModel(Employee $employee): self
    {
        return new self(
            id: $employee->id,
            company_id: $employee->company_id,
            first_name: $employee->first_name,
            last_name: $employee->last_name,
            email: $employee->email,
            start_date: $employee->start_date,
            job_title: $employee->job_title,
            status: $employee->status->value,
        );
    }
}
```

Services call `EmployeeData::fromModel($employee)` and return the DTO to the controller. The controller passes the DTO to Inertia or returns it as JSON. No model is ever passed directly to a view or response.

---

## TypeScript Auto-Generation

`spatie/laravel-typescript-transformer` reads Data classes and generates TypeScript interfaces automatically:

```bash
php artisan typescript:transform
```

Output goes to `resources/js/types/generated.d.ts`:

```typescript
// Auto-generated — do not edit manually
export interface EmployeeData {
    id: string;
    company_id: string;
    first_name: string;
    last_name: string;
    email: string;
    start_date: string;
    job_title: string | null;
    status: string;
}

export interface CreateEmployeeData {
    first_name: string;
    last_name: string;
    email: string;
    start_date: string;
    job_title: string | null;
    department_id: string | null;
}
```

Use these types in Vue components:

```typescript
import type { EmployeeData } from '@/types/generated'

const props = defineProps<{
    employee: EmployeeData
}>()
```

Never write TypeScript types by hand for any data that flows from PHP. Run `php artisan typescript:transform` after changing any Data class.

---

## Rules

1. Every controller method that accepts input uses a Data class as the parameter type — never `Request $request`
2. Every service method that returns data for a view or API uses a Data class or paginator of Data classes
3. No `$request->all()`, `$request->validated()`, or raw arrays are passed to services
4. No Eloquent model is passed directly to Inertia `render()` or `response()->json()` — always go through a DTO
5. `fromModel()` static constructor is the standard way to build output DTOs from Eloquent models
6. TypeScript types are always generated, never hand-written
