---
type: module
domain: Foundation
domain-key: foundation
panel: (scaffold)
module-key: foundation.permissions
status: complete
priority: v1-core
depends-on: [foundation.scaffold, foundation.tenancy, foundation.panels]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [seeding, policy]
tables: []
permission-prefix: ""
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Permissions Seeder

Creates all permission strings and the module catalog on initial install. Idempotent — safe to re-run after every deploy that adds a new domain.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/laravel-scaffold\|foundation.scaffold]] | spatie/permission installed (teams=true) |
| Hard | [[domains/foundation/multi-tenancy-layer\|foundation.tenancy]] | LocalDevSeeder needs CompanyContext |
| Hard | [[domains/foundation/filament-panels\|foundation.panels]] | demo logins target the panels |

---

## Core Features

- `PermissionSeeder`: creates all `domain.module.action` permission strings via `Permission::firstOrCreate()` — each module's spec `## Permissions` section is the source of the list
- `ModuleCatalogSeeder`: creates/updates `module_catalog` records with pricing (Sushi static array is primary; seeder covers the DB-backed variant if chosen)
- `LocalDevSeeder`: creates demo company, owner user, 10 demo employees — runs in `local` env only
- `DatabaseSeeder` orchestration: production seeders always, local dev seeder conditionally
- Owner role auto-syncs to all permissions via `syncPermissions(Permission::all())` — new permissions automatically available to owners without a manual re-seed

---

## Seeding Order on New Install

```bash
# 1. Run migrations
php artisan migrate

# 2. Seed permissions + module catalog
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=ModuleCatalogSeeder

# 3. Local dev only
php artisan db:seed --class=LocalDevSeeder

# Or all at once via DatabaseSeeder
php artisan migrate --seed
```

---

## Data Model

No new tables (spatie permission tables from the scaffold migration; `module_catalog`/`company_module_subscriptions` arrive with `core.billing` — LocalDevSeeder seeds free core module activations once those exist).

## DTOs

None.

## Services & Actions

Seeder classes only (see [[architecture/patterns/seeders]] for the code).

## Filament / Permissions

No panel surface. This module CREATES permission strings; it owns none.

---

## Test Checklist

- [ ] `PermissionSeeder` is idempotent — running twice creates no duplicates
- [ ] Owner role has every permission after seed (count match)
- [ ] Re-seeding after adding a new permission grants it to owner automatically
- [ ] `LocalDevSeeder` refuses to run when `APP_ENV=production`
- [ ] `migrate --seed` from empty DB completes clean (M0 exit gate)
- [ ] Demo owner login works on `/app`; demo admin on `/admin`

---

## Build Manifest

```
database/seeders/DatabaseSeeder.php
database/seeders/PermissionSeeder.php
database/seeders/ModuleCatalogSeeder.php
database/seeders/LocalDevSeeder.php (incl. LocalAdminSeeder + LocalCompanySeeder behavior)
tests/Feature/Foundation/SeederTest.php
```

---

## Related

- [[architecture/patterns/seeders]] — full seeder code
- [[architecture/auth-rbac]]
- [[domains/foundation/laravel-scaffold]]
- [[domains/core/billing-engine]] — module catalog tables
