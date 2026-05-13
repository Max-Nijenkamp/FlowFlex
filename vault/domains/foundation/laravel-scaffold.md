---
type: module
domain: Foundation
panel: (scaffold — no panel)
module-key: foundation.scaffold
status: planned
color: "#4ADE80"
---

# Laravel Scaffold

> The Laravel 13 project skeleton — packages, configuration, ULID primary keys, queue setup, and broadcasting — on which every FlowFlex domain is built.

**Domain:** Foundation
**Module key:** `foundation.scaffold`

## What It Does

The Laravel Scaffold module is the initialized Laravel 13 project with all core packages installed and configured. It establishes the conventions every other module must follow: ULID primary keys, strict types, soft deletes, PostgreSQL as the only database engine, Redis for cache/queues/sessions, and the directory structure that organises Contracts, Services, Providers, Data, Models, and Filament resources by domain.

## Features

### Core
- Laravel 13 project initialized with PHP 8.4 — strict types, readonly properties, native enums, named arguments
- Packages installed: `spatie/laravel-data` (DTOs), `spatie/laravel-permission` (RBAC teams=true), `spatie/laravel-activitylog` (audit trail)
- Laravel Horizon (queue monitoring), Laravel Reverb (WebSocket server), Laravel Pulse (health metrics), Laravel Telescope (dev only)
- PostgreSQL 17 configured as the primary database; Redis 8 for cache, queues, and sessions
- ULID primary keys on every table — `$incrementing = false`, `$keyType = 'string'`, auto-generated in model `boot()`
- Soft deletes (`SoftDeletes` trait) on all models — no hard deletes in production paths
- `spatie/laravel-permission` configured with `teams = true` in `config/permission.php` — roles scoped to `company_id`

### Advanced
- Directory skeleton: `app/Contracts/{Domain}/`, `app/Services/{Domain}/`, `app/Providers/{Domain}/`, `app/Data/{Domain}/`, `app/Models/`, `app/Events/`, `app/Filament/Admin/`, `app/Filament/App/`, `app/Support/Traits/`, `app/Support/Scopes/`, `app/Support/Services/`
- API route prefix `/api/v1/`, response format JSON with ISO 8601 timestamps
- Queue configuration: separate queues per domain (`hr`, `finance`, `crm`, `default`)
- Broadcasting configured via Reverb; channel authorization in `routes/channels.php`
- No Laravel Breeze, Jetstream, or Fortify — Filament handles all authentication

### AI-Powered
- Laravel Pulse installed for real-time application health metrics — slow queries, exception rates, queue depth, cache hit ratio surfaced in admin panel
- Telescope request inspection in development for debugging slow routes and N+1 queries

## Data Model

```erDiagram
    companies {
        ulid id PK
        string name
        string slug "unique"
        string email
        string timezone "default: UTC"
        string locale "default: en"
        string currency "default: EUR"
        string logo_path
        string favicon_path
        string primary_color
        string status
        timestamp deleted_at
        timestamps created_at/updated_at
    }

    users {
        ulid id PK
        ulid company_id FK
        string name
        string email "unique per company"
        string password
        string status
        timestamp email_verified_at
        timestamp last_login_at
        timestamp deleted_at
        timestamps created_at/updated_at
    }

    admins {
        ulid id PK
        string name
        string email "unique"
        string password
        string role
        timestamp last_login_at
        timestamp deleted_at
        timestamps created_at/updated_at
    }
```

| Table | Key Columns |
|---|---|
| `companies` | id (ULID), name, slug (unique), status, timezone, locale, currency |
| `users` | id (ULID), company_id FK, name, email, status, last_login_at |
| `admins` | id (ULID), name, email, role enum (super_admin, support, billing, developer) |

## Permissions

- `foundation.scaffold.view`
- `foundation.scaffold.manage`
- `foundation.scaffold.configure`
- `foundation.scaffold.deploy`
- `foundation.scaffold.debug`

## Filament

- **Resource:** No Filament resource (scaffold only)
- **Pages:** No custom pages
- **Custom pages:** None
- **Widgets:** None
- **Nav group:** N/A — Foundation has no user-facing panel

## Related

- [[filament-panels]]
- [[multi-tenancy-layer]]
- [[docker-environment]]
- [[test-suite]]
