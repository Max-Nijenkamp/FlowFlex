---
type: module
domain: Foundation
panel: n/a
phase: 0
status: complete
last_updated: 2026-05-09
right_brain_log: "[[builder-log-project-scaffolding]]"
---

# Project Scaffolding

The initial Laravel 13 + Filament 5 + Vue 3 + Inertia.js project. Every other module builds on top of this. Running through these steps produces a clean, multi-tenant-ready application shell with both Filament panels registered, all key packages installed, and the database foundations in place.

---

## Installation Steps

1. `composer create-project laravel/laravel flowflex` (PHP 8.4 — verify with `php -v`)
2. Install Filament 5: `composer require filament/filament`
3. Install Vue + Inertia: `npm install vue @inertiajs/vue3` and `composer require inertiajs/inertia-laravel`
4. Install Spatie packages:
   - `composer require spatie/laravel-data` — DTOs throughout the application
   - `composer require spatie/laravel-permission` — RBAC with team (company) scoping
   - `composer require spatie/laravel-activitylog` — audit log for every sensitive action
5. Configure PostgreSQL in `.env` (`DB_CONNECTION=pgsql`, set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
6. Configure Redis for cache/queues/sessions in `.env` (`CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`)
7. Install Laravel tooling:
   - `composer require laravel/horizon` — queue monitoring dashboard
   - `composer require laravel/reverb` — WebSocket server
   - `composer require laravel/pulse` — application health metrics
   - `composer require laravel/telescope` (dev only) — request inspection and debugging
8. Create `Admin` model + migration:
   - Table: `admins` (id ULID, name, email, password, role enum, last_login_at, timestamps, soft deletes)
   - Model: `App\Models\Admin` implementing `Authenticatable`
   - Separate from `users` table — FlowFlex staff never share a table with tenant users
9. Register two Filament panels:
   - `AdminPanelProvider` at `/admin`, guard `admin`, model `Admin`
   - `WorkspacePanelProvider` at `/app`, guard `web`, model `User`
   - Both providers registered in `bootstrap/providers.php`
   - Enable Filament's built-in login page on both panels (replaces all default Laravel auth)
   - Neither panel has a registration page — companies are created by FlowFlex admin in `/admin`, tenants receive an invite email with a one-time login link
10. Set up multi-tenancy foundations:
    - `BelongsToCompany` trait — adds `company_id`, applies `CompanyScope` automatically, validates ownership
    - `CompanyScope` global scope — filters all queries to the resolved company
    - `CompanyContext` service — singleton storing the current company for the request lifecycle, bound in `AppServiceProvider`
11. Register `SetCompanyContext` middleware — resolves `company_id` from the authenticated user, boots `CompanyContext`, blocks suspended companies (returns 403)
12. Configure Spatie Permission with team support:
    - In `config/permission.php` set `'teams' => true`
    - Team ID = `company_id` — roles and permissions are scoped per company
    - Run `php artisan permission:setup` then publish and run migrations

---

## Authentication — Filament Only

**Do not install Laravel Breeze, Jetstream, or Fortify.** Filament handles all authentication for both panels. There are no default Laravel auth routes (`/login`, `/register`, `/password/reset`, etc.) in this project.

| Panel | Login URL | Registration URL | Guard | Model |
|---|---|---|---|---|
| FlowFlex Admin | `/admin/login` | — | `admin` | `Admin` |
| Workspace (tenant) | `/app/login` | — | `web` | `User` |

**No registration pages exist anywhere.** Companies are created by FlowFlex staff in the `/admin` panel. Tenant owner receives an invite email. Invited team members receive a separate invite email. Both flows set their password on first login, not via a public register page.

**What to do:**
- Do not create `routes/auth.php`
- Ensure `routes/web.php` has no `Auth::routes()` call
- Both panels: `->login()->passwordReset()` only

```php
// AdminPanelProvider
->login()
->passwordReset()

// WorkspacePanelProvider
->login()
->passwordReset()
```

Unauthenticated requests to `/app/*` redirect to `/app/login`. Unauthenticated requests to `/admin/*` redirect to `/admin/login`. No other auth pages exist.

---

## Key Config

### Laravel
- **Primary keys**: ULIDs everywhere. Set `$incrementing = false`, `$keyType = 'string'` on all models. Override `boot()` to generate ULIDs automatically.
- **Soft deletes**: `SoftDeletes` trait on all models. No hard deletes in production paths.
- **Database**: PostgreSQL exclusively. No SQLite in production. Use `jsonb` for flexible data, `uuid`/`ulid` native types where available.
- **Strict types**: `declare(strict_types=1)` at the top of every PHP file.

### Filament
- Two panels, each with their own `PanelProvider` class, guard, and authenticatable model.
- Admin panel is locked to the `admin` guard — it cannot be accessed with a `web` session.
- Workspace panel uses `web` guard — tenant users never touch the admin panel.

### Spatie Permission
- `teams = true` in `config/permission.php` — this is mandatory. It scopes all roles and permissions to a team (which is the `company_id` in FlowFlex). Without this, an Owner role in Company A would grant permissions in Company B.
- The `setPermissionsTeamId($company->id)` call must happen after login and inside the `SetCompanyContext` middleware.

---

## Base Middleware Stack

```
web routes:
  auth → verified → SetCompanyContext (resolves company, applies scope, blocks suspended)

admin routes:
  auth:admin (separate guard — no company context needed)

api routes:
  auth:sanctum → SetCompanyContext
```

---

## Directory Skeleton

```
app/
├── Contracts/          # Service interfaces, one per domain module
│   ├── HR/
│   ├── Finance/
│   └── ...
├── Services/           # Concrete service implementations
│   ├── HR/
│   ├── Finance/
│   └── ...
├── Providers/          # ServiceProviders registering Contracts → Services
│   ├── HRServiceProvider.php
│   ├── FinanceServiceProvider.php
│   └── ...
├── Http/
│   └── Controllers/    # Thin Inertia controllers (< 10 lines each)
├── Data/               # spatie/laravel-data DTOs
│   ├── HR/
│   ├── Finance/
│   └── ...
├── Models/             # Eloquent models
│   ├── Admin.php       # FlowFlex staff (admins table)
│   ├── Company.php     # Tenant root
│   ├── User.php        # Tenant user (BelongsToCompany)
│   └── ...
├── Events/             # Domain events (e.g. CompanyRegistered, EmployeeHired)
├── Filament/
│   ├── Admin/          # Resources/Pages/Widgets for the /admin panel
│   │   ├── Resources/
│   │   ├── Pages/
│   │   └── Widgets/
│   └── App/            # Resources/Pages/Widgets for the /app panel
│       ├── Resources/
│       ├── Pages/
│       └── Widgets/
└── Support/
    ├── Traits/
    │   ├── BelongsToCompany.php
    │   └── HasUlid.php
    ├── Scopes/
    │   └── CompanyScope.php
    └── Services/
        └── CompanyContext.php
```

---

## Features

- Laravel 13 project initialized with PHP 8.4
- PHP 8.4 features used throughout: strict types, readonly properties, native enums, named arguments, first-class callable syntax
- Filament 5 installed with both panels registered and separated by guard/model
- Vue 3 + Inertia.js installed and configured (Vite build, SSR optional)
- spatie/laravel-data, spatie/laravel-permission (teams=true), spatie/laravel-activitylog installed
- PostgreSQL + Redis configured and verified
- ULID primary keys on all models, soft deletes everywhere
- `BelongsToCompany` trait applied to all tenant-scoped models
- `CompanyScope` global scope isolating all tenant data
- `CompanyContext` singleton service for request-scoped company resolution
- `SetCompanyContext` middleware registered on web and api middleware groups
- Two Filament panels registered: `AdminPanelProvider` (/admin) and `WorkspacePanelProvider` (/app)
- Laravel Horizon, Reverb, Pulse installed; Telescope in dev environment only

---

## Related

- [[MOC_Foundation]]
- [[admin-panel-flowflex]]
- [[workspace-panel]]
- [[multi-tenancy]]
- [[tech-stack]]
