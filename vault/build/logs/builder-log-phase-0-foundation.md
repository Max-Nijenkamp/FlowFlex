---
type: builder-log
module: phase-0-foundation
domain: Foundation
panel: (no panel — scaffold)
phase: 0
started: 2026-05-13
status: complete
color: "#F97316"
left_brain_source: "[[laravel-scaffold]], [[docker-environment]], [[filament-panels]], [[multi-tenancy-layer]], [[test-suite]]"
last_updated: 2026-05-13
---

# Builder Log — Phase 0: Foundation

All five Foundation modules built in a single session. This log covers the complete Laravel 13 + Filament 5 scaffold, Docker environment, panel setup, multi-tenancy layer, and test suite.

---

## Sessions

### 2026-05-13 — Initial Build (Complete)

**What was built:**

- `app/` — Full Laravel 13.9.0 project created via `composer create-project laravel/laravel`
- **Packages installed:**
  - `filament/filament` v5.6.3 (Filament 5, not v3 as originally specced — v5 is now the stable release)
  - `spatie/laravel-data` v4.23.0
  - `spatie/laravel-permission` v7.4.1 (teams=true enabled)
  - `spatie/laravel-activitylog` v5.0.0
  - `laravel/horizon` v5.46.0
  - `laravel/pulse` v1.7.3
  - `laravel/telescope` v5.20.0 (dev)
  - `pestphp/pest` v4.7.0
  - `pestphp/pest-plugin-laravel` v4.1.0
  - `pestphp/pest-plugin-livewire` v4.1.0
- **Support classes created:**
  - `app/Support/Traits/HasUlid.php` — ULID primary key auto-generation
  - `app/Support/Traits/BelongsToCompany.php` — company scope + auto-set company_id
  - `app/Support/Scopes/CompanyScope.php` — WHERE company_id global scope
  - `app/Support/Services/CompanyContext.php` — request-scoped company singleton
  - `app/Http/Middleware/SetCompanyContext.php` — resolves company from auth user per request
- **Models created:**
  - `app/Models/Company.php` — HasUlid + SoftDeletes + HasFactory
  - `app/Models/User.php` — HasUlid + BelongsToCompany + SoftDeletes + HasRoles + FilamentUser
  - `app/Models/Admin.php` — HasUlid + SoftDeletes + FilamentUser (admin panel only)
- **Migrations:**
  - `0001_01_01_000000_create_users_table.php` — replaced with companies + admins + users (ULID PKs, softDeletes, company FK)
  - `2026_05_13_180357_create_permission_tables.php` — modified to use `string(26)` for ULID compatibility on morph and team keys
- **Filament Panel Providers (35 total):**
  - `AdminPanelProvider` — `/admin` path, `admin` guard, Slate color
  - `AppPanelProvider` — `/app` path, `web` guard, Violet color, SetCompanyContext middleware
  - 33 domain panel providers in `app/Providers/Filament/` (Hr, Projects, Finance, Crm, Marketing, Operations, Analytics, It, Legal, Ecommerce, Comms, Lms, Ai, Community, Workplace, Psa, Plg, Travel, Esg, RealEstate, Cs, Billing, Procurement, Fpa, Events, Dms, Ethics, Field, Pricing, Risk, Support, Inbox, Partners)
- **Auth config:** `admin` guard + `admins` provider added to `config/auth.php`
- **AppServiceProvider:** `CompanyContext` bound as singleton
- **Permission config:** `teams => true` set in `config/permission.php`
- **Factories:** CompanyFactory, UserFactory (updated), AdminFactory
- **Seeders:** LocalAdminSeeder, LocalCompanySeeder, DatabaseSeeder updated
- **Docker:** `docker-compose.yml` at repo root, `app/Dockerfile`, `docker/nginx/default.conf`
- **Test suite:** `tests/Pest.php` with helpers (createCompany, createUser, createAdmin, actingAsOwner, actingAsAdmin, beforeEach cleanup)
- **Tests written:** 15 tests, all passing
  - `tests/Feature/Auth/AdminAuthTest.php` (4 tests)
  - `tests/Feature/Auth/UserAuthTest.php` (4 tests)
  - `tests/Feature/MultiTenancy/CompanyScopeTest.php` (5 tests + 1 existing example)
- **phpunit.xml:** APP_KEY set for testing, SQLite in-memory already configured

**Decisions made:**

1. Filament 5.6.3 used — see ADR: `[[decision-2026-05-13-filament-v5]]`
2. Panel providers in `app/Providers/Filament/` (not `app/Filament/Panels/`) — Filament 5 convention
3. Local `.env` uses `file`/`sync` drivers for development outside Docker (Redis not available locally)
4. Permission migration uses `string(26)` for ULID compatibility — see ADR: `[[decision-2026-05-13-permission-ulid]]`

**Test results:** 15 / 15 passed (100%)

---

## Gaps Discovered

None critical. One deviation noted and captured as ADR (Filament 5 `authModel()` API does not exist).

---

## Files Created

```
app/
  Dockerfile
  app/
    Http/Middleware/SetCompanyContext.php
    Models/Admin.php
    Models/Company.php
    Models/User.php (replaced)
    Providers/AppServiceProvider.php (updated)
    Providers/Filament/AdminPanelProvider.php (updated)
    Providers/Filament/AppPanelProvider.php
    Providers/Filament/[33 domain providers].php
    Support/Scopes/CompanyScope.php
    Support/Services/CompanyContext.php
    Support/Traits/BelongsToCompany.php
    Support/Traits/HasUlid.php
  bootstrap/providers.php (updated — 35 providers)
  config/auth.php (updated — admin guard)
  config/permission.php (updated — teams=true)
  database/factories/AdminFactory.php
  database/factories/CompanyFactory.php
  database/factories/UserFactory.php (updated)
  database/migrations/0001_01_01_000000_create_users_table.php (replaced)
  database/migrations/2026_05_13_180357_create_permission_tables.php (updated)
  database/seeders/DatabaseSeeder.php (updated)
  database/seeders/LocalAdminSeeder.php
  database/seeders/LocalCompanySeeder.php
  tests/Pest.php
  tests/Feature/Auth/AdminAuthTest.php
  tests/Feature/Auth/UserAuthTest.php
  tests/Feature/MultiTenancy/CompanyScopeTest.php
docker/
  nginx/default.conf
docker-compose.yml
```
