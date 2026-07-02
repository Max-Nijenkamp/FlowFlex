---
domain: foundation
module: laravel-scaffold
type: module
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Laravel Scaffold

`foundation.scaffold` — the Laravel 13 project skeleton. ULID PKs, strict types, soft deletes, PostgreSQL-only, Redis for cache/queues/sessions, domain-organised directory layout. The first thing built; nothing exists before it.

| Field | Value (verified against `app/`) |
|---|---|
| PHP | `^8.3` (composer.json — **not 8.4**) |
| Framework | `laravel/framework:^13.8` |
| Auth | No Breeze/Jetstream/Fortify — Filament handles auth |
| PK strategy | ULID everywhere via `HasUlids` |
| Drivers | cache/queue/session = Redis, DB = pgsql |

> [!note] Corrected from flat spec
> The old spec claimed PHP 8.4. composer.json requires `^8.3` (CI tests 8.3/8.4/8.5). The `users` table uses `first_name`/`last_name` with a `(company_id, email)` unique index — both verified in migration.

## Notes split out

- [[data-model|Data model]] — companies / users / admins tables (verified)
- [[infrastructure|Install manifest]] — package set + build order
- [[decisions|Decisions]] — no-auth-starter-kit, ULID, flat foldering

## Build Manifest

```
composer.json / package.json
config/{database,queue,cache,session,filesystems,broadcasting}.php
database/migrations/0001_01_01_000000_create_companies_table.php
database/migrations/0001_01_01_000001_create_users_table.php
database/migrations/0001_01_01_000002_create_admins_table.php
app/Models/{Company,User,Admin}.php
database/factories/{CompanyFactory,UserFactory,AdminFactory}.php
phpunit.xml (sqlite :memory: override)
pint.json / phpstan.neon
tests/Architecture/{LayersTest,ModelsTest}.php
```

Verified present: all three `0001_01_01_*` migrations, both panel providers, the four seeders, the three Architecture tests.

## Test Checklist (verified by suite)

- [x] `php artisan migrate` creates companies/users/admins with ULID PKs
- [x] Arch test: models use `HasUlids` + `SoftDeletes` (`tests/Architecture/ModelsTest.php`)
- [x] Arch test: no `dd`/`dump` in `app/` (`tests/Architecture/LayersTest.php`)
- [x] Config: queue/cache/session = redis; DB = pgsql (`tests/Feature/ScaffoldTest.php`)

## Related

- [[../docker-environment/_module|Docker Environment]] — containerises this scaffold
- [[../multi-tenancy-layer/_module|Multi-Tenancy Layer]]
- [[../../../architecture/tech-stack]]
- [[../../../architecture/data-model]]
- [[../../../glossary]]
