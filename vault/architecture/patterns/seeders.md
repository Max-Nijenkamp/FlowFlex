---
type: architecture
category: patterns
pattern-key: seeding
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Seeders — Permissions and Module Catalog

Two types of seeders in FlowFlex: **production seeders** (run in every environment on install, idempotent) and **local dev seeders** (run only in `local`, create test accounts).

---

## Production Seeders (always run)

### 1. PermissionSeeder

Creates every permission string for every domain. Must be run after every deployment that adds a new domain or module.

```php
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Core
            'core.audit.view-any', 'core.audit.view',
            'core.settings.view', 'core.settings.update',
            'core.billing.view', 'core.billing.manage',
            'core.marketplace.activate', 'core.marketplace.deactivate',
            'core.rbac.manage-roles', 'core.rbac.manage-users',
            'core.webhooks.manage',
            'core.api.manage',
            'core.privacy.manage-dsars',
            'access.app-panel',

            // HR
            'hr.employees.view-any', 'hr.employees.view',
            'hr.employees.create', 'hr.employees.update', 'hr.employees.delete',
            'hr.leave.view-any', 'hr.leave.view', 'hr.leave.create',
            'hr.leave.approve', 'hr.leave.reject',
            'hr.payroll.view-any', 'hr.payroll.run', 'hr.payroll.approve',
            'hr.onboarding.manage',
            'hr.org.view',
            'hr.self-service.view',
            'hr.recruitment.view-any', 'hr.recruitment.create', 'hr.recruitment.manage',
            'hr.performance.manage',
            'hr.analytics.view',
            'access.hr-panel',

            // Finance
            'finance.invoices.view-any', 'finance.invoices.view',
            'finance.invoices.create', 'finance.invoices.update',
            'finance.invoices.send', 'finance.invoices.void', 'finance.invoices.approve',
            'finance.expenses.view-any', 'finance.expenses.view',
            'finance.expenses.create', 'finance.expenses.approve',
            'finance.ledger.view', 'finance.ledger.post-manual',
            'finance.bank.view', 'finance.bank.reconcile',
            'finance.budgets.view', 'finance.budgets.manage',
            'finance.reporting.view',
            'finance.payroll-journal.post',
            'access.finance-panel',

            // CRM
            'crm.contacts.view-any', 'crm.contacts.view',
            'crm.contacts.create', 'crm.contacts.update', 'crm.contacts.delete',
            'crm.accounts.view-any', 'crm.accounts.view',
            'crm.accounts.create', 'crm.accounts.update',
            'crm.deals.view-any', 'crm.deals.view', 'crm.deals.view-all',
            'crm.deals.create', 'crm.deals.update',
            'crm.pipeline.view',
            'crm.quotes.create', 'crm.quotes.send',
            'crm.activities.view-any', 'crm.activities.create',
            'crm.sequences.manage',
            'crm.forecasting.view',
            'access.crm-panel',

            // (Phase 2 domains added when their build starts)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
```

`firstOrCreate` makes this idempotent — safe to run multiple times.

### 2. ModuleCatalogSeeder (if not using Sushi)

If `module_catalog` is a real DB table (rather than Sushi static data), seed it:

```php
class ModuleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            // Always-free core modules
            ['module_key' => 'core.auth',          'name' => 'Authentication', 'per_user_monthly_price' => 0.00],
            ['module_key' => 'core.notifications', 'name' => 'Notifications',  'per_user_monthly_price' => 0.00],
            ['module_key' => 'core.audit',         'name' => 'Audit Log',      'per_user_monthly_price' => 0.00],
            ['module_key' => 'core.files',         'name' => 'File Storage',   'per_user_monthly_price' => 0.00],
            ['module_key' => 'core.rbac',          'name' => 'Roles & Permissions', 'per_user_monthly_price' => 0.00],
            ['module_key' => 'core.settings',      'name' => 'Company Settings', 'per_user_monthly_price' => 0.00],
            ['module_key' => 'core.marketplace',   'name' => 'Module Marketplace', 'per_user_monthly_price' => 0.00],

            // Paid HR modules
            ['module_key' => 'hr.profiles',   'name' => 'Employee Profiles',  'per_user_monthly_price' => 1.50],
            ['module_key' => 'hr.leave',      'name' => 'Leave Management',   'per_user_monthly_price' => 1.00],
            ['module_key' => 'hr.onboarding', 'name' => 'Onboarding',         'per_user_monthly_price' => 0.75],
            ['module_key' => 'hr.payroll',    'name' => 'Payroll',            'per_user_monthly_price' => 2.50],
            // ...
        ];

        foreach ($modules as $module) {
            ModuleCatalog::updateOrCreate(
                ['module_key' => $module['module_key']],
                array_merge($module, ['domain' => explode('.', $module['module_key'])[0], 'is_active' => true]),
            );
        }
    }
}
```

---

## Local Dev Seeders (environment: local only)

```php
class LocalDevSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) return;

        // FlowFlex staff admin
        Admin::factory()->create([
            'name' => 'FlowFlex Admin',
            'email' => 'admin@flowflex.nl',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
        ]);

        // Demo company + owner
        $company = Company::factory()->create([
            'name' => 'FlowFlex Demo',
            'slug' => 'demo',
            'subscription_status' => 'active',
        ]);

        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        // Seed free core modules for demo company
        foreach (['core.auth', 'core.notifications', 'core.audit', 'core.files', 'core.rbac', 'core.settings', 'core.marketplace'] as $key) {
            CompanyModuleSubscription::create(['company_id' => $company->id, 'module_key' => $key, 'activated_at' => now()]);
        }

        // Activate HR modules for demo
        foreach (['hr.profiles', 'hr.leave', 'hr.onboarding', 'hr.payroll'] as $key) {
            CompanyModuleSubscription::create(['company_id' => $company->id, 'module_key' => $key, 'activated_at' => now()]);
        }

        // Owner user
        $ownerRole = Role::create(['name' => 'owner', 'team_id' => $company->id]);
        $ownerRole->syncPermissions(Permission::all());

        $owner = User::factory()->create([
            'company_id' => $company->id,
            'first_name' => 'Max',
            'last_name' => 'Nijenkamp',
            'email' => 'demo@flowflex.nl',
            'password' => bcrypt('password'),
        ]);
        $owner->assignRole($ownerRole);

        // 10 demo employees
        Employee::factory()->count(10)->for($company)->create();
    }
}
```

---

## DatabaseSeeder

```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Always run (idempotent)
        $this->call([
            PermissionSeeder::class,
            ModuleCatalogSeeder::class,
        ]);

        // Local dev only
        if (app()->environment('local')) {
            $this->call(LocalDevSeeder::class);
        }
    }
}
```

---

## Adding Permissions for a New Domain

When a new domain build starts:

1. Add permission strings to `PermissionSeeder`
2. Run `php artisan db:seed --class=PermissionSeeder` on all environments
3. The owner role automatically gets new permissions via `syncPermissions(Permission::all())`
