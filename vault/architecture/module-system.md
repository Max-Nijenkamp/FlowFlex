---
type: architecture
category: pattern
color: "#A78BFA"
---

# Module System

The module system controls which features a company has access to. It is the enforcement layer between the pricing model and the application's resource access.

---

## Tables

### `module_catalog`

Platform-level table — not scoped to any company. Defines all available modules and their pricing.

| Column | Type | Description |
|---|---|---|
| `id` | ULID | Primary key |
| `module_key` | string | Unique identifier, format: `panel.module` (e.g. `hr.payroll`, `crm.pipeline`) |
| `domain` | string | Domain slug (e.g. `hr`, `finance`, `crm`) |
| `name` | string | Human-readable display name (e.g. "HR Payroll") |
| `per_user_monthly_price` | decimal(8,2) | Price in EUR per active user per month. `0.00` for core/free modules |
| `is_active` | boolean | Whether available for new activations. `false` hides from marketplace but does not deactivate existing subscribers |

### `company_module_subscriptions`

Per-company activation records. One row per module per company.

| Column | Type | Description |
|---|---|---|
| `id` | ULID | Primary key |
| `company_id` | ULID FK | The company this activation belongs to |
| `module_key` | string | Matches `module_catalog.module_key` |
| `activated_at` | timestamp | When the module was turned on |
| `deactivated_at` | timestamp | When the module was turned off (null if still active) |
| `activated_by` | ULID FK | User who performed the activation |

---

## Module Key Format

Module keys follow the format `panel.module`:

```
hr.profiles           — HR employee profiles module
hr.payroll            — HR payroll module
hr.leave              — HR leave management module
projects.kanban       — Projects kanban board module
projects.time-tracking — Projects time tracking module
finance.invoicing     — Finance invoicing module
crm.pipeline          — CRM sales pipeline module
core.audit-log        — Core audit log (always free)
```

The `panel` segment matches the Filament panel's path slug. The `module` segment matches the specific module within that domain. This naming convention is enforced in `BillingService::hasModule()` calls and in the `canAccess()` check on each resource.

---

## BillingService::hasModule()

The central access check used by every gated Filament resource:

```php
class BillingService
{
    public static function hasModule(string $moduleKey): bool
    {
        $company = app(CompanyContext::class)->current();

        return CompanyModuleSubscription::where('company_id', $company->id)
            ->where('module_key', $moduleKey)
            ->whereNull('deactivated_at')
            ->exists();
    }
}
```

This is called inside `canAccess()` on every Filament resource and page within a domain panel:

```php
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.payroll.view-any')
        && BillingService::hasModule('hr.payroll');
}
```

Both conditions must pass. A user with the correct permission but no module subscription is denied. A user with an active module subscription but no permission is also denied.

---

## Module Marketplace

The Module Marketplace is a custom Filament page in the `/app` panel, accessible to users with the `owner` or `admin` role. It shows all modules in `module_catalog` where `is_active = true`, grouped by domain.

Each module card displays:
- Module name and domain
- Feature description
- Per-user monthly price (or "Included" for free modules)
- Activation toggle (on/off)

Activation creates a `company_module_subscriptions` record with `activated_at = now()` and `deactivated_at = null`.

Deactivation sets `deactivated_at = now()` on the existing record. Data is retained. Reactivation creates a new subscription record — the previous record remains as a history entry.

---

## EnforceModuleAccess Middleware

For non-Filament routes (API and any web routes outside the panel), `EnforceModuleAccess` middleware checks the module key registered for that route:

```php
Route::middleware(['auth:sanctum', 'module:hr.payroll'])
    ->get('/api/v1/payroll', [PayrollController::class, 'index']);
```

The middleware resolves the module key from the route parameter and calls `BillingService::hasModule()`. Returns `403 Forbidden` if the module is not active.

---

## Free / Core Modules

Core platform modules are always active and never appear in the marketplace activation flow. They have `per_user_monthly_price = 0.00` in `module_catalog` and are seeded as active subscriptions for every company at creation:

| Module Key | Name |
|---|---|
| `core.auth` | Authentication & Identity |
| `core.notifications` | Notifications & Alerts |
| `core.audit-log` | Audit Log |
| `core.file-storage` | File Storage |
| `core.rbac` | Roles & Permissions |
| `core.settings` | Company Settings |
| `core.marketplace` | Module Marketplace |

---

## Module Pricing Admin

Module prices are managed from the `/admin` panel by FlowFlex staff. Price changes apply globally to all companies at the start of the next billing month. No per-company pricing overrides exist in the current data model (a `price_lock_until` column on `company_module_subscriptions` is noted as a future addition for enterprise agreements at scale).
