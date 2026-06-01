---
type: module
domain: Foundation
panel: (scaffold)
module-key: foundation.permissions
status: planned
color: "#4ADE80"
---

# Permissions Seeder

Creates all permission strings and the module catalog on initial install. Idempotent — safe to re-run after every deploy that adds a new domain.

---

## Core Features

- `PermissionSeeder`: creates all `domain.module.action` permission strings via `Permission::firstOrCreate()`
- `ModuleCatalogSeeder`: creates/updates `module_catalog` records with pricing (if not using Sushi static data)
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

## Filament

No Filament resources — infrastructure only.

See [[architecture/patterns/seeders]] for the full seeder code.

---

## Related

- [[architecture/patterns/seeders]]
- [[architecture/auth-rbac]]
- [[domains/foundation/laravel-scaffold]]
