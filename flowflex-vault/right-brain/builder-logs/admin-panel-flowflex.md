---
type: builder-log
module: admin-panel-flowflex
domain: Foundation
color: "#F97316"
status: complete
built_date: 2026-05-09
last_updated: 2026-05-09
---

# Builder Log — Admin Panel (FlowFlex Internal)

The `/admin` Filament panel is operational. FlowFlex staff can log in at `/admin/login` using the `admin` guard.

---

## Files Created

### Panel Provider
- `app/Providers/Filament/AdminPanelProvider.php`
  - Path: `/admin`
  - Guard: `admin`
  - Login + password reset enabled
  - No registration page
  - Brand: "FlowFlex Admin", primary color: Orange

### Resources
- `app/Filament/Admin/Resources/CompanyResource.php`
  - List with status badge, user count, module count
  - Create form: company details + owner details section (visible on create only)
  - Edit form: company details only
  - Table actions: Activate, Suspend, Cancel (with confirmation)
  - Calls `CompanyCreationService` on create, `CompanyService` on status changes
- `app/Filament/Admin/Resources/AdminUserResource.php`
  - Create/Edit: name, email, password, role (super_admin|support|billing|developer)
  - Password hashed via `bcrypt()`
- `app/Filament/Admin/Resources/ModuleCatalogResource.php`
  - Full CRUD for module pricing
  - module_key, domain, name, per_user_monthly_price, is_active
- `app/Filament/Admin/Resources/PlatformAnnouncementResource.php`
  - Target: all or specific company
  - Send action marks `sent_at`
  - Only draft announcements can be edited
- `app/Filament/Admin/Resources/CompanyFeatureFlagResource.php`
  - company_id nullable (null = global flag)
  - Uses `withoutGlobalScopes()` in table query

### Resource Pages (15 files)
All standard List/Create/Edit pages for each resource above.

---

## Admin Routes Verified

```
GET /admin/login
GET /admin/companies
GET /admin/companies/create
GET /admin/companies/{record}/edit
GET /admin/admin-users
GET /admin/admin-users/create
GET /admin/admin-users/{record}/edit
GET /admin/module-catalogs
GET /admin/company-feature-flags
GET /admin/platform-announcements
```

---

## Auth Model

`App\Models\Admin` uses `admin` guard, separate `admins` table, `HasUlids`, `SoftDeletes`.
Auth config in `config/auth.php` has `admins` provider pointing to `Admin::class`.

---

## Session — 2026-05-09 (Test DB Isolation — Root Cause Fixed)

### Changes Made

**tests/TestCase.php**
- Overrode `createApplication()` to sync `$_ENV → $_SERVER` before calling `bootstrap()`
- Root cause: PHPUnit `<env force="true">` sets `$_ENV` and calls `putenv()` but does NOT update `$_SERVER`. Laravel's Dotenv repository uses `$_SERVER` as the primary reader. Docker process-level env `$_SERVER['DB_DATABASE'] = 'flowflex'` persisted even after PHPUnit forced `$_ENV['DB_DATABASE'] = 'flowflex_testing'`, causing `RefreshDatabase` to run `migrate:fresh` against the live database.
- Fix: loop `$_ENV` and set each key in `$_SERVER` before bootstrapping — this ensures `flowflex_testing` wins in the repository reader chain
- After fix: 74/74 tests pass, live `flowflex` DB is untouched (2 admins + 1 user preserved)

**phpunit.xml**
- `DB_DATABASE=flowflex_testing force="true"` retained
- `DB_URL` cleared with `force="true"` to prevent any URL-based connection override

**.env.testing**
- Points to `flowflex_testing` PostgreSQL database (separate from live `flowflex`)
- All monitoring disabled (`PULSE_ENABLED=false`, `TELESCOPE_ENABLED=false`, `NIGHTWATCH_ENABLED=false`)

### Investigation Path

1. Confirmed `flowflex_testing` database exists and `phpunit.xml` has `force="true"`
2. Manual PHP test: `putenv` + `$_ENV` set correctly — but `env('DB_DATABASE')` still returned `flowflex`
3. Added `$_SERVER` to the test → `env('DB_DATABASE')` returned `flowflex_testing` ✅
4. Root cause confirmed: Laravel Dotenv repository reads `$_SERVER` first; Docker process env lives there

### Verified

- `74 passed (113 assertions)` — all tests green
- Live DB after test run: 2 admins, 1 user, companies intact

---

## Session — 2026-05-09 (Panel Styling + Layout Fixes)

### Changes Made

**AdminPanelProvider.php**
- Added `->maxContentWidth(Width::Full)` — fixes "half page used" layout issue
- Added `->sidebarCollapsibleOnDesktop()` — sidebar can collapse for more working space
- Replaced `'Support'` nav group with `'Team'` — `AdminUserResource` uses `'Team'` group; nothing used `'Support'`
- Imported `Filament\Support\Enums\Width`

**WorkspacePanelProvider.php**
- Added `->maxContentWidth(Width::Full)` — same full-width fix
- Added `->sidebarCollapsibleOnDesktop()`
- Added `->navigationGroups([NavigationGroup::make('Settings')])` — Users, Roles, CompanySettings, ModuleMarketplace all declare `'Settings'` group but panel had none defined
- Imported `Filament\Navigation\NavigationGroup` and `Filament\Support\Enums\Width`

**phpunit.xml**
- Added `force="true"` on `DB_CONNECTION`, `DB_DATABASE`, `DB_URL` env vars
- Required because Docker container sets `DB_CONNECTION=pgsql` as process-level env vars which normally override phpunit.xml `<env>` without `force`

### Problem Found and Fixed — config:cache Breaks Test Isolation

Running `php artisan config:cache` bakes PostgreSQL connection settings into a static file. Even with `force="true"`, phpunit env overrides have no effect on cached config. When tests ran after a `config:cache` call, they used the live PostgreSQL database — 4 Livewire authenticate tests failed with `SQLSTATE[25P02]` (transaction aborted by prior INSERT failure in wrong DB context).

**Fix**: `php artisan config:clear` before running tests. Never run `config:cache` on the dev Docker instance; it must be cleared before test runs.

### All 74 Tests Pass

After clearing config cache: `74 passed (113 assertions)` — all Feature and Unit tests green.

---

## Related
- [[admin-panel-flowflex]]
- [[workspace-panel]]
- [[project-scaffolding]]
- [[entity-admin]]
