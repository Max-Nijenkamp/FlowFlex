---
type: architecture
category: tenancy
pattern-key: modules
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Module System

Controls which features a company has access to. The enforcement layer between pricing and application resource access.

---

## Module Key Format

`panel.module` — e.g. `hr.payroll`, `finance.invoicing`, `crm.pipeline`

The `panel` segment matches the Filament panel's path slug. The `module` segment identifies the specific module within that domain.

---

## Tables

### `module_catalog`

Platform-level — not company-scoped. Backed by `calebporzio/sushi` static array (no migration needed for catalog entries — defined in PHP code, not the database).

| Column | Type | Description |
|---|---|---|
| `module_key` | string (unique) | e.g. `hr.payroll` |
| `domain` | string | e.g. `hr` |
| `name` | string | Display name in marketplace |
| `per_user_monthly_price` | decimal | EUR, `0.00` for free core modules |
| `is_active` | boolean | `false` hides from marketplace but does not deactivate existing subscribers |

### `company_module_subscriptions`

Per-company activation records. One row per activation event.

| Column | Type | Description |
|---|---|---|
| `company_id` | ulid FK | Tenant |
| `module_key` | string | Matches `module_catalog.module_key` |
| `activated_at` | timestamp | When activated |
| `deactivated_at` | timestamp | null = still active |
| `activated_by` | ulid FK | User who activated |

Deactivation sets `deactivated_at = now()` — data is retained. Reactivation creates a new row — previous row stays as history.

---

## BillingService::hasModule()

The single gating method called by every `canAccess()`:

```php
class BillingService
{
    public function hasModule(string $moduleKey): bool
    {
        $companyId = app(CompanyContext::class)->currentId();

        // Result is cached — see architecture/caching
        $activeModules = Cache::remember(
            "company:{$companyId}:modules",
            now()->addMinutes(5),
            fn () => CompanyModuleSubscription::query()
                ->where('company_id', $companyId)
                ->whereNull('deactivated_at')
                ->pluck('module_key')
                ->toArray()
        );

        return in_array($moduleKey, $activeModules);
    }
}
```

Cached at 5 minutes — activation takes effect within 5 minutes for all panel users.

Cache invalidated immediately on activation/deactivation:

```php
Cache::forget("company:{$companyId}:modules");
```

---

## canAccess() Pattern

Both conditions must pass:

```php
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.payroll.view-any')     // Spatie permission
        && BillingService::hasModule('hr.payroll');      // module subscription
}
```

---

## API Middleware

For non-Filament routes, use `EnforceModuleAccess` middleware:

```php
Route::middleware(['auth:sanctum', 'module:hr.payroll'])
    ->get('/api/v1/payroll', [PayrollController::class, 'index']);
```

Returns `403 Forbidden` if module not active.

---

## Always-Free Core Modules

Seeded as active for every new company. Cannot be deactivated:

| Module Key | Name |
|---|---|
| `core.auth` | Authentication & Identity |
| `core.notifications` | Notifications |
| `core.audit` | Audit Log |
| `core.files` | File Storage |
| `core.rbac` | Roles & Permissions |
| `core.settings` | Company Settings |
| `core.marketplace` | Module Marketplace |

---

## Company Seeding on Creation

When FlowFlex staff create a new company in `/admin`:

```php
class CompanyCreationService
{
    public function create(CreateCompanyData $data): Company
    {
        $company = Company::create($data->toArray());

        // Seed free core modules
        foreach (ModuleCatalog::freeCoreModules() as $key) {
            CompanyModuleSubscription::create([
                'company_id' => $company->id,
                'module_key' => $key,
                'activated_at' => now(),
            ]);
        }

        // Create owner role
        $role = Role::create(['name' => 'owner', 'team_id' => $company->id]);
        $role->syncPermissions(Permission::all());
        $owner->assignRole($role);

        return $company;
    }
}
```

---

## Module Pricing Administration

Managed in `/admin` by FlowFlex staff. Price changes apply globally at the start of the next billing month. No per-company price overrides in v1 data model.

---

## Related

- [[product/pricing-model]]
- [[domains/core/billing-engine/_module]]
- [[domains/core/module-marketplace/_module]]
- [[architecture/caching]] — module list caching
