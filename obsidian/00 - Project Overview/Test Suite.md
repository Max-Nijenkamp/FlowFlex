# Test Suite

> **Status:** 344 passing · 0 skipped · 0 failing  
> **Runner:** Pest PHP v4 (PHPUnit 12.5 underneath)  
> **Last run:** 2026-05-07  

---

## Run

```bash
php artisan test           # sequential
php artisan test --parallel  # fast (parallel workers)
```

---

## Structure

```
tests/
├── Unit/
│   ├── BelongsToCompanyTest.php         — trait auto-sets company_id, CompanyScope fires
│   ├── CompanyScopeTest.php             — scope only fires when tenant authed
│   ├── GeneratePayslipPdfJobTest.php    — job fetches payrun, marks generated_at
│   ├── TotalDaysCalculationTest.php     — leave request day calculation (weekdays + half-day)
│   └── Models/                          — model-level unit tests
│
├── Feature/
│   ├── Phase1/
│   │   ├── AdminAuthTest.php            — User login/logout, wrong password, disabled account
│   │   ├── TenantAuthTest.php           — Tenant login, wrong guard, panel access
│   │   ├── MultiTenancyTest.php         — Company isolation: tenant A can't see tenant B's data
│   │   ├── RbacTest.php                 — Role/permission assignment and enforcement
│   │   ├── ApiKeyAuthTest.php           — API key middleware: valid, invalid, missing
│   │   ├── FileStorageTest.php          — File model CRUD, URL resolution, S3 path never exposed
│   │   ├── NotificationPreferenceTest.php — Per-tenant notification preference CRUD
│   │   └── WorkspaceSettingsTest.php    — Company settings get/set
│   │
│   ├── Phase2/
│   │   ├── Hr/
│   │   │   ├── DepartmentTest.php       — CRUD, parent/child, self-reference prevention
│   │   │   ├── EmployeeTest.php         — CRUD, full_name accessor, company isolation
│   │   │   ├── LeaveTypeTest.php        — CRUD, code uniqueness per company
│   │   │   ├── LeaveRequestTest.php     — Create, approve, reject (requires reason), total_days calc
│   │   │   ├── OnboardingTemplateTest.php — Template CRUD, task ordering
│   │   │   ├── OnboardingFlowTest.php   — Flow creation from template, task completion
│   │   │   ├── PayrollEntityTest.php    — CRUD, default entity flag
│   │   │   ├── PayElementTest.php       — CRUD, element_type enum validation
│   │   │   ├── PayRunTest.php           — Create, generate payslips job dispatch
│   │   │   └── SalaryRecordTest.php     — CRUD, effective_date ordering
│   │   │
│   │   └── Projects/
│   │       ├── TaskLabelTest.php        — CRUD, color validation
│   │       ├── TaskTest.php             — CRUD, status transitions, assignee
│   │       ├── TimeEntryTest.php        — Create with tenant guard, duration calc
│   │       ├── TimesheetTest.php        — CRUD, period validation
│   │       ├── DocumentFolderTest.php   — CRUD, parent/child nesting
│   │       └── DocumentTest.php        — Upload, URL access, never raw S3
│   │
│   └── Events/
│       ├── HrEventsTest.php             — EmployeeCreated, LeaveApproved, PayRunGenerated fire
│       └── ProjectsEventsTest.php       — TaskCreated, TaskStatusChanged fire
```

---

## Skipped Tests

None — all 344 tests pass.

---

## Conventions

- All tests use `RefreshDatabase` — SQLite in-memory (local) / PostgreSQL (CI)
- Multi-tenancy: every test creates its own `Company` + `Tenant` via factories
- Cross-company isolation checked by creating two companies and asserting data separation
- Factories live in `database/factories/` — one per model following `{Model}Factory` naming

---

## App Bugs Found and Fixed by Tests

| Bug | Fix |
|-----|-----|
| `Spatie\Activitylog\Traits\LogsActivity` wrong namespace (43 models) | Changed to `Models\Concerns\LogsActivity` |
| `Spatie\Activitylog\LogOptions` wrong namespace (43 models) | Changed to `Support\LogOptions` |
| `Tenant` missing `getFilamentName()` | Added method returning `fullName()` |
| `Employee::getFullNameAttribute()` double-space on null `middle_name` | Rewrote using `collect()->filter()->implode(' ')` |
| `TextInput::uppercase()` not in Filament 5 (PayrollEntityResource, PublicHolidayResource) | Replaced with `->dehydrateStateUsing(strtoupper)` |
| `Filament\Tables\Actions\Action` class doesn't exist in Filament 5 | Changed to `Filament\Actions\Action` in LeaveRequestResource, ManageApiKeys, ManageTeam |
| `Filament\Tables\Actions\EditAction` class doesn't exist in Filament 5 | Changed to `Filament\Actions\EditAction` in ManageTeam |
| `<x-filament-panels::form>` and `<x-filament-panels::form.actions>` Blade components don't exist | Replaced with plain HTML `<form>` + `<x-filament::actions>` in workspace settings views |
| Navigation group `Settings` + items both had icons — Filament 5 forbids both | Removed `$navigationIcon` from all 4 workspace settings pages |
| `Tenant` implements `FilamentUser` but not `HasName` — Filament checks `instanceof HasName` | Added `implements HasName` interface to Tenant model |
| `OnboardingTemplateResource` relation `templateTasks` vs `tasks` mismatch | Fixed to `tasks` |
| `LeaveRequest` missing `total_days` on create | Added `mutateFormDataBeforeCreate` |
| `TimeEntry` and `Timesheet` used wrong guard `auth()->id()` | Fixed to `auth('tenant')->id()` |
| `GeneratePayslipPdf` job bypassed global scope | Added `withoutGlobalScopes()` + explicit `company_id` |
| `BelongsToCompany` read API company from wrong request bag | Fixed to `request()->attributes->get('api_company')` |
| `company_module` pivot ULID not auto-generated | Added `CompanyModule` pivot model with `HasUlids` |
| SQLite can't DROP PRIMARY KEY column in migration | Rewrote migration with driver-check + `DB::statement()` |

---

## Adding Tests

1. Put feature tests in `tests/Feature/Phase{N}/{Domain}/`
2. Use `pest()->uses(RefreshDatabase::class)` at top of file (or in `Pest.php` for the directory)
3. Factory: `Company::factory()->create()` → `Tenant::factory()->for($company)->create()`
4. Authenticate: `actingAs($tenant, 'tenant')` for workspace panels, `actingAs($user)` for admin
5. Permissions: call `$tenant->givePermissionTo('module.resource.action')` in test setup
