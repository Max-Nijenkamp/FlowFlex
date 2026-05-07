---
tags: [brain, patterns, conventions]
last_updated: 2026-05-07
---

# Patterns

How FlowFlex code actually works. These are enforced patterns — not aspirational. Every new file must follow these.

---

## Model Pattern

```php
namespace App\Models\Hr;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;  // NOT Traits\LogsActivity
use Spatie\Activitylog\Support\LogOptions;            // NOT LogOptions from root

class Employee extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = ['company_id', 'first_name', ...];

    protected function casts(): array
    {
        return [
            'start_date'             => 'date',
            'national_id_encrypted'  => 'encrypted',   // sensitive: use encrypted cast
            'password_hash'          => 'hashed',       // password fields: use hashed cast
            'employment_status'      => EmploymentStatus::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email'])  // NEVER log encrypted/sensitive fields
            ->logOnlyDirty();
    }
}
```

**Spatie namespace trap (v5):** Must use `Models\Concerns\LogsActivity` and `Support\LogOptions` — not the root namespace ones.

---

## BelongsToCompany Behaviour

- Adds `WHERE company_id = :current_company` to every query automatically
- Fires only when the `tenant` guard is authenticated
- Admin panel uses `withoutGlobalScopes()` + explicit `where('company_id', ...)` — never just raw `withoutGlobalScopes()`
- Queue jobs use `withoutGlobalScopes()` + explicit `company_id` in conditions — add a comment explaining why

```php
// In admin widgets — OK
Company::withoutGlobalScopes()->count()

// In queue job — OK with comment
// Queue context has no auth — scope must be explicit
Payslip::withoutGlobalScopes()->where('company_id', $this->payRun->company_id)->firstOrCreate([...])

// In tenant panel — NEVER do this
Employee::withoutGlobalScopes()->get() // leaks other companies' data
```

---

## Filament 5 Resource Pattern

```php
namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Enums\NavigationGroup;
use Filament\Actions\Action;           // NOT Filament\Tables\Actions\Action (doesn't exist)
use Filament\Actions\EditAction;       // NOT Filament\Tables\Actions\EditAction
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;           // NOT Filament\Forms\Form (Filament 3 API)
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static \UnitEnum|string|null $navigationGroup = NavigationGroup::People;
    protected static ?int $navigationSort = 1;

    // ALWAYS implement all four — never rely on Filament defaults
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.employees.view') ?? false;
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.employees.create') ?? false;
    }
    public static function canEdit($record): bool
    {
        return auth()->user()?->can('hr.employees.edit') ?? false;
    }
    public static function canDelete($record): bool
    {
        return auth()->user()?->can('hr.employees.delete') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Details')->schema([...]),
        ]);
    }

    // ALWAYS override to eager-load — no exceptions
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['department', 'manager']);
    }
}
```

**Navigation group icon rule:** If a `NavigationGroup` has an icon set, none of its member resources may also have `$navigationIcon`. Filament 5 throws a runtime exception.

---

## Tenant Dropdown Pattern

`Tenant` has no `BelongsToCompany` — it IS the auth model, so global scope never applies to it. Always scope manually:

```php
// WRONG — leaks all tenants from all companies
Select::make('tenant_id')->options(fn () => Tenant::pluck('email', 'id'))

// CORRECT
Select::make('tenant_id')
    ->options(fn () => Tenant::where('company_id', auth()->user()?->company_id)
        ->get()
        ->mapWithKeys(fn ($t) => [$t->id => "{$t->first_name} {$t->last_name}"]))
    ->searchable()
```

Note: `Tenant` has no `name` column. Labels must be built from `first_name` + `last_name` (or `email` as fallback).

---

## Policy Pattern

```php
namespace App\Policies\Hr;

use App\Models\Hr\Employee;
use App\Models\Tenant;

class EmployeePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.employees.view');
    }

    public function view(Tenant $tenant, Employee $employee): bool
    {
        // Always check company_id on the record — never trust the ID alone
        return $tenant->company_id === $employee->company_id
            && $tenant->can('hr.employees.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.employees.create');
    }

    public function update(Tenant $tenant, Employee $employee): bool
    {
        return $tenant->company_id === $employee->company_id
            && $tenant->can('hr.employees.edit');
    }

    public function delete(Tenant $tenant, Employee $employee): bool
    {
        return $tenant->company_id === $employee->company_id
            && $tenant->can('hr.employees.delete');
    }

    public function restore(Tenant $tenant, Employee $employee): bool { return false; }
    public function forceDelete(Tenant $tenant, Employee $employee): bool { return false; }
}
```

Register in `AppServiceProvider::registerPolicies()`:
```php
Gate::policy(Employee::class, EmployeePolicy::class);
```

---

## Event + Listener Pattern

```php
// Event
class LeaveApproved
{
    public function __construct(public readonly LeaveRequest $leaveRequest) {}
}

// Listener
class NotifyEmployeeLeaveApproved implements ShouldQueue
{
    public function handle(LeaveApproved $event): void
    {
        $employee = $event->leaveRequest->employee;
        if (!$employee?->tenant) return; // always null-guard
        $employee->tenant->notify(new LeaveApprovedNotification($event->leaveRequest));
    }
}
```

Register in `EventServiceProvider::$listen`. All listeners implement `ShouldQueue` — no exceptions.

---

## API Controller Pattern

```php
class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // ALWAYS get company from request attributes — never auth()->user() (no auth in API)
        $company = $request->attributes->get('api_company');

        $items = Employee::where('company_id', $company->id)
            ->with('department')
            ->orderBy('last_name')
            ->paginate(25);

        return response()->json([
            'data' => $items->map(fn ($e) => [...]),
            'meta' => [
                'total'        => $items->total(),
                'per_page'     => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
            ],
        ]);
    }
}
```

---

## Migration Pattern

```php
Schema::create('employees', function (Blueprint $table) {
    $table->ulid('id')->primary();                          // ULID, not auto-increment
    $table->ulid('company_id');
    $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

    $table->string('first_name');
    $table->string('email')->nullable();
    // ...

    $table->index(['company_id', 'status']);                // compound index for list views
    $table->index('created_at');

    $table->softDeletes();
    $table->timestamps();
});
```

---

## Factory Pattern

```php
namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\Invoice;
use App\Enums\Finance\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'number'     => 'INV-' . $this->faker->unique()->numerify('####'),
            'status'     => InvoiceStatus::Draft,
            // ...
        ];
    }

    // States make tests readable
    public function sent(): static
    {
        return $this->state(['status' => InvoiceStatus::Sent]);
    }

    public function paid(): static
    {
        return $this->state(['status' => InvoiceStatus::Paid, 'paid_amount' => $this->faker->randomFloat(2, 100, 5000)]);
    }
}
```

---

## Activity Log Rules

- Use `->logOnly([...])` with explicit whitelist — never `->logFillable()` or `->logAll()`
- Never log: `*_encrypted` fields, `password_hash`, `gross_pay`, `net_pay`, `salary`
- Spatie logs the raw (pre-cast) value — so encrypted fields would log plaintext if included

---

## Permission Naming

Format: `{module}.{resource}.{action}`

Examples:
```
hr.employees.view
hr.employees.create
hr.leave-requests.approve
finance.invoices.send
crm.tickets.resolve
projects.task-labels.edit
```

Every permission used in a `can*()` method must exist in `RolesAndPermissionsSeeder`.

---

## Language / Translation Pattern

**Supported languages:** English (`en`), Dutch (`nl`), German (`de`)  
French and Spanish are **removed** — do not add them back.

**Marketing frontend (Vue/Inertia):** English-only. No language toggle. No `locale` switching in the Vue frontend. `vue-i18n` stays at `locale: 'en'` permanently. `t()` calls in Vue pages resolve English only.

**Filament panels:** Fully translatable via `SetLocaleFromCompany` middleware → `App::setLocale(company->locale)`. Language switcher in every panel (bezhansalleh/filament-language-switch). Flag images at `public/flags/{gb,nl,de}.svg`.

**Translation key convention** (all Filament custom strings):
```
hr.navigation.groups.people              → navigation group label
hr.resources.departments.label           → singular model label
hr.resources.departments.plural          → plural model label
hr.resources.departments.sections.details → Section::make() title
hr.resources.departments.fields.manager  → form field ->label()
hr.resources.departments.columns.manager → table column ->label()
hr.resources.departments.filters.status  → filter ->label()
```

**Domain → translation file mapping:**
| Domain | File |
|---|---|
| HR panel | `lang/{en,nl,de}/hr.php` |
| Projects panel | `lang/{en,nl,de}/projects.php` |
| Finance panel | `lang/{en,nl,de}/finance.php` |
| CRM panel | `lang/{en,nl,de}/crm.php` |
| Admin panel | `lang/{en,nl,de}/admin.php` |
| Workspace panel | `lang/{en,nl,de}/workspace.php` |
| Laravel core | `lang/{en,nl,de}/{auth,pagination,passwords,validation,messages}.php` |
| Filament core UI | `vendor/filament/*/resources/lang/{nl,de}/` — loaded automatically |

**NavigationGroup enum pattern** (all 4 domain enums: Hr, Projects, Finance, Crm):
```php
public function label(): string
{
    return match ($this) {
        self::People => __('hr.navigation.groups.people'),
        // ...
    };
}
```

**Resource pattern** (all resources — do NOT use `$navigationGroup` static property):
```php
public static function getNavigationGroup(): ?string
{
    return NavigationGroup::People->label();
}

public static function getModelLabel(): string
{
    return __('hr.resources.employees.label');
}

public static function getPluralModelLabel(): string
{
    return __('hr.resources.employees.plural');
}
```

**Field/column labels** — always use explicit `__()` key, NOT `->translateLabel()`:
```php
->label(__('hr.resources.employees.fields.first_name'))  // CORRECT
->label('First Name')                                    // WRONG — hardcoded
->translateLabel()                                       // WRONG — calls __('First Name') which fails
```

**Language enum:** `App\Enums\Language` — cases `EN`, `NL`, `DE` only. No `FR`, no `ES`.

**Locale middleware:** `App\Http\Middleware\SetLocaleFromCompany` — runs on all tenant panel requests, sets `App::setLocale()` from `company->locale`.
