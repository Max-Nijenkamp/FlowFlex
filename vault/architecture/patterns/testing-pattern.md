---
type: architecture
category: patterns
pattern-key: testing
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Testing Pattern

Pest PHP. Integration tests by default — real SQLite in-memory database, full stack from controller/service to database.

---

## Coverage Scope — 80% of What

The 80% line-coverage target applies to **app code that contains decisions**:

| Layer | Counted in coverage | How tested |
|---|---|---|
| `Services/`, `Actions/` | ✅ yes | feature tests through the service/action |
| `Listeners/`, `Jobs/`, console commands | ✅ yes | dispatch + assert effects |
| `States/` (transition guards, side effects) | ✅ yes | via service tests that trigger transitions |
| `Data/` custom rules + casts | ✅ yes | form/DTO validation tests |
| Filament resources/pages | ➖ behavior only | `pest-plugin-livewire` per the spec Test Checklist — form validation, create/edit flows, `canAccess` — NOT coverage-counted |
| Models (relationships, casts, scopes) | ➖ indirect | covered through the above; no dedicated getter tests |
| Migrations, providers, config | ❌ no | `migrate:fresh --seed` in CI is the test |
| Framework + Filament internals | ❌ never | testing the framework is waste |

**Per-module floor** (regardless of %): every box in the spec's `## Test Checklist`, always including tenant-isolation + module-gating. A module with 90% coverage but no tenant-isolation test fails the definition of done ([[architecture/way-of-working]]).

**Intentionally not tested**: Filament table column formatting, navigation/icon config, Blade markup, third-party package behavior, getters with no logic.

---

## Setup

`phpunit.xml` overrides database connection:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

```php
// tests/Pest.php
uses(RefreshDatabase::class)->in('Feature');
```

Every test starts clean. No test database server required.

---

## CompanyContext Setup

Required in every test touching a model with `BelongsToCompany`:

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

**Set `CompanyContext` before creating any model** — the factory's `creating` hook reads it.

---

## Factory Pattern

```php
class EmployeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'email'      => $this->faker->safeEmail(),
            'start_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'status'     => EmployeeStatus::Active,
        ];
    }
}
```

Always pass an explicit company in tests:
```php
// Correct
Employee::factory()->count(5)->for($company)->create();

// Wrong — creates a new company per employee
Employee::factory()->count(5)->create();
```

---

## Filament Panel Tests

```php
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

```php
it('returns 403 when module is not active', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);
    $user = User::factory()->for($company)->withRole('owner')->create();

    actingAs($user)->get('/hr/payroll')->assertForbidden();
});

it('returns 200 when module is active', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);

    CompanyModuleSubscription::factory()->for($company)->create([
        'module_key' => 'hr.payroll',
        'activated_at' => now(),
    ]);

    $user = User::factory()->for($company)->withRole('owner')->create();

    actingAs($user)->get('/hr/payroll')->assertOk();
});
```

---

## Rate Limiter Isolation

```php
beforeEach(function () {
    RateLimiter::clear('api');
    RateLimiter::clear('login');
});
```

Without this, auth endpoint tests fail intermittently after the fifth run.

---

## No Database Mocking

FlowFlex does not mock the database. Real SQLite in-memory only.

Reasons:
1. Mocking Eloquent produces tests that test the mock, not the code
2. SQLite in-memory is fast — full suite under 60 seconds
3. Real queries catch scope issues, index problems, and constraint violations that mocks cannot

**Exception**: external HTTP services (Stripe, email providers, third-party APIs) are always mocked via `Http::fake()` or dedicated fake classes. Never make real HTTP calls in tests.

---

## Factory States

Every model factory should define states for common status variations:

```php
class LeaveRequestFactory extends Factory
{
    public function pending(): static
    {
        return $this->state(['status' => 'submitted']);
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => User::factory(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(['status' => 'rejected']);
    }
}

// Usage in tests
$approvedRequest = LeaveRequest::factory()->approved()->for($company)->create();
$pending = LeaveRequest::factory()->pending()->for($company)->count(3)->create();
```

---

## Livewire / Filament Tests (pest-plugin-livewire)

```php
use function Pest\Livewire\livewire;

it('validates required fields on employee create form', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);
    setPermissionsTeamId($company->id);

    $user = User::factory()->for($company)->withRole('admin')->create();

    livewire(CreateEmployee::class)
        ->actingAs($user)
        ->fillForm(['first_name' => '', 'email' => 'not-an-email'])
        ->call('create')
        ->assertHasFormErrors([
            'first_name' => 'required',
            'email' => 'email',
        ]);
});

it('creates an employee successfully', function () {
    $company = Company::factory()->create();
    app(CompanyContext::class)->set($company);

    $user = User::factory()->for($company)->withRole('admin')->create();

    livewire(CreateEmployee::class)
        ->actingAs($user)
        ->fillForm([
            'first_name' => 'Max',
            'last_name'  => 'Nijenkamp',
            'email'      => 'max@example.com',
            'start_date' => '2026-01-01',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect('/hr/employees');

    expect(Employee::where('email', 'max@example.com')->exists())->toBeTrue();
});
```

---

## Architecture Tests

Pest architecture tests enforce structural rules — run as fast as unit tests, catch violations before code review:

```php
// tests/Architecture/LayersTest.php

arch('controllers do not import service implementations')
    ->expect('App\Http\Controllers')
    ->not->toUse('App\Services');

arch('services implement their interface')
    ->expect('App\Services')
    ->toImplement('App\Contracts');

arch('models use required traits')
    ->expect('App\Models\HR')
    ->toUseTrait('App\Support\Traits\BelongsToCompany');

arch('no debug functions in production code')
    ->expect('App')
    ->not->toUse(['dd', 'dump', 'ray', 'var_dump', 'print_r']);

arch('actions use the AsAction trait')
    ->expect('App\Actions')
    ->toUseTrait('Lorisleiva\Actions\Concerns\AsAction');

arch('events carry company_id')
    ->expect('App\Events')
    ->toHaveProperty('company_id');
```

---

## Email Testing

```php
use Illuminate\Support\Facades\Mail;

it('queues leave approved email', function () {
    Mail::fake();

    $request = LeaveRequest::factory()->for($company)->create();

    ApproveLeaveRequest::run($request, approvedBy: $manager);

    Mail::assertQueued(LeaveApprovedMail::class, function ($mail) use ($request) {
        return $mail->hasTo($request->employee->email);
    });

    // Confirm not sent synchronously
    Mail::assertNothingSent();
});
```

---

## Tenant Isolation Test

Every domain should include a cross-tenant isolation test:

```php
it('does not leak data between companies', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();

    app(CompanyContext::class)->set($companyA);
    $employeeA = Employee::factory()->for($companyA)->create();

    app(CompanyContext::class)->set($companyB);
    $employeeB = Employee::factory()->for($companyB)->create();

    $userA = User::factory()->for($companyA)->withRole('owner')->create();

    // CompanyA user cannot see CompanyB employees
    app(CompanyContext::class)->set($companyA);
    actingAs($userA)
        ->get('/api/v1/employees')
        ->assertOk()
        ->assertJsonMissing(['id' => $employeeB->id]);

    expect(Employee::all())->toHaveCount(1); // CompanyScope filters to CompanyA
});
