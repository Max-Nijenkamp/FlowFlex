---
type: architecture
category: pattern
color: "#A78BFA"
---

# DTO Pattern (spatie/laravel-data)

`spatie/laravel-data` Data classes replace both `FormRequest` (input validation) and `JsonResource` (output serialisation) in a single class.

---

## File Location

```
app/Data/{Domain}/{Model}Data.php
```

Examples: `app/Data/HR/EmployeeData.php`, `app/Data/Finance/InvoiceData.php`

One DTO per concern. Separate input and output DTOs when fields differ significantly.

---

## Input DTO

```php
namespace App\Data\HR;

use Spatie\LaravelData\Attributes\Validation\{Date, Email, Max, Required, StringType};
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

Laravel resolves and validates the DTO from the request when type-hinted in a controller method. Invalid input returns `422` before the controller body runs.

---

## Output DTO

```php
namespace App\Data\HR;

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

Services call `EmployeeData::fromModel($employee)` and return the DTO. No model is ever passed directly to a view or response.

---

## TypeScript Auto-Generation

```bash
docker exec flowflex_app php artisan typescript:transform
```

Output: `resources/js/types/generated.d.ts`

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
```

Use in Vue components:
```typescript
import type { EmployeeData } from '@/types/generated'
const props = defineProps<{ employee: EmployeeData }>()
```

Never write TypeScript types by hand for server-to-client data.

---

## Rules

1. Controller input: always use a Data class — never `Request $request` or `$request->all()`
2. Controller output: always return a Data class or paginator of Data classes to Inertia / JSON
3. No Eloquent model passed directly to `inertia()` or `response()->json()`
4. `fromModel()` is the standard static constructor for output DTOs
5. Run `typescript:transform` after changing any Data class
