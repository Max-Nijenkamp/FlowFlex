---
type: builder-log
module: project-scaffolding
domain: Foundation
color: "#F97316"
status: complete
built_date: 2026-05-09
last_updated: 2026-05-09
---

# Builder Log — Project Scaffolding

Phase 0 Foundation build session. Produced the complete Laravel 13 + Filament 4 scaffold.

---

## What Was Built

### Packages Installed
- `filament/filament ^5.0` (Filament 5 v5.6.2 — upgraded from initial Filament 4 install before Phase 1 began; see [[decision-2026-05-09-filament-5-upgrade]])
- `inertiajs/inertia-laravel ^3.1`
- `spatie/laravel-data ^4.0`
- `spatie/laravel-permission ^6.0` (teams=true)
- `spatie/laravel-activitylog ^5.0`
- `spatie/laravel-medialibrary ^11.0`
- `stripe/stripe-php ^14.0`
- `laravel/horizon ^5.x`
- `laravel/reverb ^1.x`
- `laravel/pulse ^1.x`
- `laravel/telescope ^5.x` (dev only)
- `vue ^3.5`, `@inertiajs/vue3 ^2.0`, `@vitejs/plugin-vue`, `typescript`, `tailwindcss ^4.0`

### Migrations (Foundation range 000001–000007)
- `000001_create_companies_table.php`
- `000002_create_admins_table.php`
- `000003_create_users_table.php`
- `000004_create_company_module_subscriptions_table.php`
- `000005_create_module_catalog_table.php`
- `000006_create_platform_announcements_table.php`
- `000007_create_company_feature_flags_table.php`

All migrations verified passing with `php artisan migrate:fresh`.

### Models
- `app/Models/Company.php` — HasUlids, SoftDeletes, HasFactory
- `app/Models/Admin.php` — HasUlids, SoftDeletes, Authenticatable
- `app/Models/User.php` — HasUlids, SoftDeletes, BelongsToCompany, HasRoles
- `app/Models/CompanyModuleSubscription.php` — HasUlids
- `app/Models/ModuleCatalog.php` — HasUlids
- `app/Models/PlatformAnnouncement.php` — HasUlids
- `app/Models/CompanyFeatureFlag.php` — HasUlids

### Multi-Tenancy Layer
- `app/Support/Traits/BelongsToCompany.php` — Global scope + auto company_id
- `app/Support/Scopes/CompanyScope.php` — Filters all queries by company_id
- `app/Support/Services/CompanyContext.php` — Singleton for request-scoped company
- `app/Http/Middleware/SetCompanyContext.php` — Sets context after auth
- `app/Exceptions/MissingCompanyContextException.php`

### Auth Configuration
- `config/auth.php` — `web` guard (users), `admin` guard (admins), `sanctum` guard
- `config/permission.php` — `teams: true` enabled

### Filament Panels
- `app/Providers/Filament/AdminPanelProvider.php` — `/admin`, `admin` guard
- `app/Providers/Filament/WorkspacePanelProvider.php` — `/app`, `web` guard

### Admin Panel Resources (`/admin`)
- `CompanyResource` — Full CRUD, suspend/activate/cancel actions
- `AdminUserResource` — FlowFlex staff management
- `ModuleCatalogResource` — Module pricing catalog
- `PlatformAnnouncementResource` — Create/send announcements
- `CompanyFeatureFlagResource` — Feature flag management per company or global

### Workspace Panel Pages/Resources (`/app`)
- `Dashboard` — Personal dashboard
- `CompanySettings` — Company settings form
- `ModuleMarketplace` — Enable/disable modules
- `UserResource` — User invite, roles, deactivate
- `RoleResource` — Role creation and permission assignment

### Services
- `app/Services/Foundation/CompanyCreationService.php` — Full company + owner creation in DB transaction
- `app/Services/Foundation/CompanyService.php` — CRUD + lifecycle actions

### DTOs
- `app/Data/Foundation/CreateCompanyData.php`
- `app/Data/Foundation/UpdateCompanyData.php`
- `app/Data/Foundation/InviteUserData.php`

### Events
- `app/Events/Foundation/CompanyCreated.php`
- `app/Events/Foundation/UserInvited.php`
- `app/Events/Foundation/UserActivated.php`

### Contracts
- `app/Contracts/Foundation/CompanyServiceInterface.php`

### Service Providers
- `app/Providers/AppServiceProvider.php` — CompanyContext singleton binding
- `app/Providers/Foundation/FoundationServiceProvider.php` — CompanyServiceInterface binding

### Seeders
- `database/seeders/DatabaseSeeder.php` — Creates super_admin
- `database/seeders/ModuleCatalogSeeder.php` — Seeds 92 modules across all domains

### Frontend
- `vite.config.js` — Vue 3 + Inertia + Tailwind 4 config
- `resources/js/app.js` — Inertia Vue bootstrap

### Blade Views
- `resources/views/filament/app/pages/company-settings.blade.php`
- `resources/views/filament/app/pages/module-marketplace.blade.php`

---

## Verification

```
php artisan migrate:fresh    → all 10 migrations pass (7 custom + 3 default + permissions)
php artisan db:seed          → ModuleCatalogSeeder: 92 modules seeded
php artisan route:list       → admin/* and app/* routes all registered
```

---

## Notes

- Installed Filament 5 v5.6.2 (upgraded from initial Filament 4 install before Phase 1 began). Filament 5 is fully compatible with Laravel 13.
- Filament 5 uses `Filament\Schemas\Schema` for the `form()` method signature in Resources. `Section` is `Filament\Schemas\Components\Section`. Form field components still use `Filament\Forms\Components\*`.
- `$view` on `Filament\Pages\Page` is a non-static property — must use `getView()` override, not `protected static string $view`.
- Navigation group: use `getNavigationGroup()` method override (not static property).
- Spatie Permission teams mode enabled — `setPermissionsTeamId($company->id)` must be called before any role/permission operations.
- `.env` defaults to SQLite for local development. Change to PostgreSQL for production.

---

## Related Left-Brain Specs
- [[project-scaffolding]]
- [[admin-panel-flowflex]]
- [[workspace-panel]]
- [[multi-tenancy]]
- [[auth-rbac]]
- [[tech-stack]]
