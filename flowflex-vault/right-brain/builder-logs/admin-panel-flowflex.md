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

## Session — 2026-05-09 (Phase 0 Audit — Bugs, Security, Indexes)

### Audit Scope

Full Phase 0 audit run by `analyst` subagent — checked all 5 modules for completeness, security, bugs, and scalability.

### Bugs Fixed

**UserResource deactivate double-fire (HIGH)**
- `app/Filament/App/Resources/UserResource.php` — action called `$record->update(['status' => 'deactivated'])` AND `$record->delete()` simultaneously. Deactivated users became invisible in the UI (soft-deleted AND status-changed). Removed `$record->delete()` — status-only deactivation is the correct behaviour.

**country field silently dropped (MEDIUM)**
- `app/Filament/Admin/Resources/CompanyResource.php` — form had `TextInput::make('country')` and `CreateCompanyData` had `$country` DTO field, but no `country` column existed in the `companies` table and the model had no `country` in `$fillable`. CompanyCreationService also didn't write it. Fix: new migration `000008_add_country_to_companies_table.php` adds `country` nullable string column, model `$fillable` updated, `CompanyCreationService` now writes `$data->country`.

**AdminFactory default role invalid (MEDIUM)**
- `database/factories/AdminFactory.php` — default role was `'admin'` which is not a valid enum value (valid: `super_admin`, `support`, `billing`, `developer`). Factory-created admins without `->superAdmin()` state had a non-existent role. Changed default to `'support'`.

### Security Fixes

**Email uniqueness not company-scoped (MEDIUM)**
- `app/Filament/App/Resources/UserResource.php:63` and `UsersRelationManager.php:39` — both used `->unique(User::class, 'email', ignoreRecord: true)` which checked email uniqueness globally across all companies. The DB unique constraint is `(company_id, email)` — same email can legitimately exist in different companies. Fixed with `modifyRuleUsing: fn (Unique $rule) => $rule->where('company_id', ...)` scoped to the current company.

**CompanySettings slug missing uniqueness validation (MEDIUM)**
- `app/Filament/App/Pages/CompanySettings.php:68` — tenants could set their slug to match another company's, causing routing ambiguity. Added `->unique(Company::class, 'slug', ignorable: fn () => app(CompanyContext::class)->current())`.

**bcrypt redundancy removed (LOW)**
- `AdminUserResource.php` and `UsersRelationManager.php` — `dehydrateStateUsing(fn ($s) => bcrypt($s))` was pre-hashing passwords before the `'password' => 'hashed'` model cast. The `hashed` cast uses `Hash::isHashed()` and skips re-hashing already-hashed values, so no double-hash occurred, but `bcrypt()` was bypassing the app-configured round count. Removed `dehydrateStateUsing` — the model cast now handles hashing exclusively.

### Performance Indexes Added (migration 000009)

- `companies.status` — used in `SetCompanyContext` middleware and admin table filter
- `company_module_subscriptions.status` — used in `activeModuleKeys()` called on every app page load
- `module_catalog.domain` — used in `ModuleMarketplace::getModules()` orderBy
- `module_catalog.is_active` — used in WHERE clause of `ModuleMarketplace::getModules()`

### Gaps Discovered

- [[gap_company-context-queue-singleton]] (severity: high) — CompanyContext singleton leaks state across Horizon worker jobs
- [[gap_invite-token-cache-only]] (severity: medium) — Invite tokens only in Redis cache; cache flush = permanent lockout
- [[gap_announcement-send-stub]] (severity: medium) — PlatformAnnouncement "Send" action marks sent_at but dispatches nothing
- [[gap_missing-critical-path-tests]] (severity: medium) — No tests for CompanyCreationService, ModuleMarketplace, CompanySettings

### All 74 Tests Pass

After all fixes: `74 passed (113 assertions)` — all Feature and Unit tests green. Live DB untouched.

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
