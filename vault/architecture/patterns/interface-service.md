---
type: architecture
category: pattern
color: "#A78BFA"
---

# Interface / Service Pattern

Every domain module follows the same structural pattern: a contract interface, a service provider that binds the concrete implementation, a concrete service class, and a thin controller that only touches the interface.

---

## Why This Pattern

The interface is the boundary. Tests mock the interface, not the service. Controllers depend on the interface, not the concrete class. If the implementation changes — or is swapped for a different strategy — the controller and tests are unaffected.

It also makes the service provider the single location where the concrete class name appears in a non-service file. Searching for `EmployeeService::class` (not the interface) anywhere outside `EmployeeServiceProvider` is a signal of a pattern violation.

---

## File Structure Per Domain

```
app/
├── Contracts/
│   └── HR/
│       └── EmployeeServiceInterface.php
├── Services/
│   └── HR/
│       └── EmployeeService.php
├── Providers/
│   └── HR/
│       └── HrServiceProvider.php          ← one provider per domain
├── Http/
│   └── Controllers/
│       └── Hr/
│           └── EmployeeController.php
├── Data/
│   └── HR/
│       ├── EmployeeData.php
│       └── CreateEmployeeData.php
└── Models/
    └── HR/
        └── Employee.php
```

---

## Interface

```php
namespace App\Contracts\HR;

use App\Data\HR\CreateEmployeeData;
use App\Data\HR\EmployeeData;
use App\Data\HR\ListEmployeesData;
use App\Models\HR\Employee;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeServiceInterface
{
    public function list(ListEmployeesData $data): LengthAwarePaginator;
    public function find(string $id): Employee;
    public function create(CreateEmployeeData $data): Employee;
    public function update(string $id, UpdateEmployeeData $data): Employee;
    public function delete(string $id): void;
}
```

The return types use Eloquent models where returning a typed model reference is appropriate, and DTOs or paginators where the output needs to be serialisable.

---

## Service

```php
namespace App\Services\HR;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Events\HR\EmployeeHired;
use App\Models\HR\Employee;

class EmployeeService implements EmployeeServiceInterface
{
    public function create(CreateEmployeeData $data): Employee
    {
        $employee = Employee::create($data->toArray());
        event(new EmployeeHired(
            company_id: $employee->company_id,
            employee_id: $employee->id,
            user_id: auth()->id(),
            start_date: $data->start_date,
            job_title: $data->job_title ?? '',
        ));
        return $employee;
    }
}
```

Events are fired from the service, never from the controller. The controller does not know events exist.

---

## ServiceProvider

One `ServiceProvider` per domain binds all of that domain's interfaces:

```php
namespace App\Providers\HR;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Contracts\HR\LeaveServiceInterface;
use App\Services\HR\EmployeeService;
use App\Services\HR\LeaveService;
use Illuminate\Support\ServiceProvider;

class HrServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->bind(LeaveServiceInterface::class, LeaveService::class);
    }
}
```

Registered in `bootstrap/providers.php`. The concrete class names appear only here.

---

## Controller

```php
namespace App\Http\Controllers\Hr;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Data\HR\ListEmployeesData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly EmployeeServiceInterface $employees,
    ) {}

    public function index(ListEmployeesData $data): Response
    {
        return inertia('HR/Employees/Index', [
            'employees' => $this->employees->list($data),
        ]);
    }

    public function store(CreateEmployeeData $data): RedirectResponse
    {
        $this->employees->create($data);
        return redirect()->route('hr.employees.index');
    }
}
```

The controller injects the interface. It never imports `EmployeeService` directly. Total method line count stays under 10.

---

## Rules

1. Controllers never touch Eloquent models directly
2. Services never return raw Eloquent collections to controllers — use DTOs or paginators
3. Events are fired from services, never from controllers
4. DTOs are used for all input (no `$request->all()`)
5. The ServiceProvider is the only place where the concrete service class name appears outside the service itself
6. Tests mock the interface with a fake or mock, never the concrete service
