---
type: architecture
category: pattern
color: "#A78BFA"
---

# Interface / Service Pattern

Use this pattern for: complex domain operations, services with multiple methods, operations that need testable swappable implementations, or cross-domain dependencies.

For simple single-step operations, use [[architecture/patterns/actions-pattern]] instead.

---

## When to Use This vs Actions

| Use Interface→Service when | Use Actions when |
|---|---|
| Service has 3+ methods | Single operation, single method |
| Multiple implementations may exist | One implementation, no swap needed |
| Used by multiple callers | Called from one or two places |
| Cross-domain dependency injection | Simple internal operation |
| e.g. `EmployeeService` (CRUD + reporting) | e.g. `SendWelcomeEmail`, `DeactivateModule` |

---

## File Structure

```
app/
├── Contracts/HR/
│   └── EmployeeServiceInterface.php
├── Services/HR/
│   └── EmployeeService.php
├── Providers/HR/
│   └── HrServiceProvider.php       ← one provider per domain
├── Http/Controllers/Hr/
│   └── EmployeeController.php
├── Data/HR/
│   ├── EmployeeData.php
│   └── CreateEmployeeData.php
└── Models/HR/
    └── Employee.php
```

---

## Interface

```php
namespace App\Contracts\HR;

interface EmployeeServiceInterface
{
    public function list(ListEmployeesData $data): LengthAwarePaginator;
    public function find(string $id): Employee;
    public function create(CreateEmployeeData $data): Employee;
    public function update(string $id, UpdateEmployeeData $data): Employee;
    public function delete(string $id): void;
}
```

---

## Service

```php
namespace App\Services\HR;

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

Events are fired from the service, never from the controller.

---

## ServiceProvider

One provider per domain binds all of that domain's interfaces:

```php
namespace App\Providers\HR;

class HrServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->bind(LeaveServiceInterface::class, LeaveService::class);
    }
}
```

Registered in `bootstrap/providers.php`. Concrete class names appear **only here**.

---

## Controller

```php
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

Controller injects the interface. Never imports the concrete service. Under 10 lines per method.

---

## Rules

1. Controllers never touch Eloquent directly
2. Services never return raw Eloquent collections to controllers — use DTOs or paginators
3. Events fired from services, never from controllers
4. DTOs for all input — no `$request->all()`
5. ServiceProvider is the only file where the concrete service class name appears outside the service
6. Tests mock the interface, never the concrete service
