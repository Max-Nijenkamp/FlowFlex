---
type: architecture
category: pattern
color: "#A78BFA"
---

# Testing Pattern

FlowFlex uses Pest PHP for all tests. Tests are integration tests by default — they run against a real SQLite in-memory database (overriding the Docker PostgreSQL environment) and test the full stack from controller/service down to the database.

---

## Test Runner

**Pest PHP** — configured in `phpunit.xml` / `pest.config.php`. All tests are in `tests/Feature/` (integration) and `tests/Unit/` (pure logic, no database). There are no mock-heavy unit tests for services — if the service touches the database, write a Feature test.

---

## Database Setup

`phpunit.xml` overrides the database connection to SQLite in-memory:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Each test class that touches the database uses `RefreshDatabase` to rebuild the schema from migrations:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->in('Feature');
```

This means every test starts with a clean database, runs against real migrations, and tears down after. No test database server required, no state leakage between tests.

---

## CompanyContext Setup

Every test that touches a model with `BelongsToCompany` must set `CompanyContext` before accessing the model. Without it, `MissingCompanyContextException` is thrown (or in edge cases, queries return empty due to missing scope):

```php
it('lists employees for the current company', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);
    setPermissionsTeamId($company->id);

    $user = User::factory()->for($company)->create();
    Employee::factory()->count(3)->for($company)->create();

    actingAs($user)
        ->get('/hr/employees')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('employees.data', 3));
});
```

**Always set CompanyContext before creating any model with BelongsToCompany** — not just before the assertion. The factory's `creating` hook in `BelongsToCompany` reads the context when creating the model.

---

## Factory Pattern

Every model has a factory. Factories must set `company_id`:

```php
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),   // creates a company if none given
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'email'      => $this->faker->safeEmail(),
            'start_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'status'     => EmployeeStatus::Active,
        ];
    }
}
```

In tests, always pass an explicit company to the factory to avoid creating orphaned companies:

```php
$company = Company::factory()->create();
app(CompanyContext::class)->set($company);

// Correct — all employees belong to the same company
Employee::factory()->count(5)->for($company)->create();

// Wrong — creates a new company per employee
Employee::factory()->count(5)->create();
```

---

## Filament Panel Tests

Test Filament resources by hitting their panel URLs as authenticated users:

```php
it('can view the employee list page', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);
    setPermissionsTeamId($company->id);

    $user = User::factory()->for($company)->withRole('owner')->create();

    actingAs($user)
        ->get('/hr/employees')
        ->assertOk();
});

it('can create an employee', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);
    setPermissionsTeamId($company->id);

    $user = User::factory()->for($company)->withRole('owner')->create();

    Livewire::actingAs($user)
        ->test(CreateEmployee::class)
        ->fillForm([
            'first_name' => 'Max',
            'last_name'  => 'Nijenkamp',
            'email'      => 'max@example.com',
            'start_date' => '2026-01-01',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Employee::where('email', 'max@example.com')->exists())->toBeTrue();
});
```

---

## Module Gating Tests

When testing a gated resource or page, activate the module before the test. Without an active `CompanyModuleSubscription`, `canAccess()` returns false and the request returns 403:

```php
it('returns 403 when payroll module is not active', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);
    $user = User::factory()->for($company)->withRole('owner')->create();

    actingAs($user)
        ->get('/hr/payroll')
        ->assertForbidden();
});

it('returns 200 when payroll module is active', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);

    CompanyModuleSubscription::factory()->for($company)->create([
        'module_key' => 'hr.payroll',
        'activated_at' => now(),
    ]);

    $user = User::factory()->for($company)->withRole('owner')->create();

    actingAs($user)
        ->get('/hr/payroll')
        ->assertOk();
});
```

---

## Rate Limiter Isolation

API tests that hit rate-limited endpoints must clear the rate limiter between tests or they will fail after the first few runs:

```php
beforeEach(function () {
    RateLimiter::clear('api');
    RateLimiter::clear('login');
});
```

Call `RateLimiter::clear($key)` with the limiter key used in `RouteServiceProvider`. Without this, the test suite will intermittently fail after the fifth run of auth endpoint tests.

---

## No Database Mocking

FlowFlex does not mock the database. All persistence tests use the real SQLite in-memory database. The reasons:

1. Mocking Eloquent queries produces tests that test the mock, not the code
2. SQLite in-memory is fast enough — a full test suite run should be under 60 seconds
3. Real queries catch scope issues, index problems, and constraint violations that mocks cannot

The one exception: external HTTP services (Stripe, email providers, third-party APIs) are always mocked via Laravel's `Http::fake()` or dedicated fake classes. Never make real HTTP calls in tests.
