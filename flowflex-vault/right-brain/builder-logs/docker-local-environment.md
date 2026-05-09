---
type: builder-log
module: docker-local-environment
domain: Foundation
panel: n/a
phase: 0
started: 2026-05-09
status: complete
color: "#F97316"
left_brain_source: "[[docker-local-environment]]"
last_updated: 2026-05-09
---

# Builder Log — Docker & Local Environment

Phase 0 Foundation — Docker Compose local stack, monitoring gates, and local seeders.

---

## Files Created

### Docker
- `app/Dockerfile` — PHP 8.4 FPM (alpine), installs pdo_pgsql, redis ext, pcntl, gd, zip, composer, npm, builds frontend assets
- `app/docker-compose.yml` — 7 services: app, nginx, postgres:17, redis:8, mailpit, horizon, reverb
- `app/docker/nginx/default.conf` — Nginx reverse proxy to PHP-FPM on port 9000

### Monitoring Gates (`app/Providers/AppServiceProvider.php`)
- `Horizon::auth()` — only `Admin` model instances can access `/horizon`
- `Gate::define('viewPulse')` — only `Admin` model instances can access `/pulse`
- `Gate::define('viewTelescope')` — only `Admin` model instances can access `/telescope`

### Admin Panel Navigation (`app/Providers/Filament/AdminPanelProvider.php`)
- `NavigationGroup` — "System Health" group with server icon
- `NavigationItem` — Horizon → `/horizon` (new tab), queue-list icon
- `NavigationItem` — Pulse → `/pulse` (new tab), chart-bar icon
- `NavigationItem` — Telescope → `/telescope` (new tab), magnifying-glass icon — **visible only in `local` env**

### Local Seeders
- `database/seeders/LocalAdminSeeder.php` — Admin `test@test.nl` / `test1234`, role: `super_admin`
- `database/seeders/LocalCompanySeeder.php` — Company "FlowFlex Demo" (slug: flowflex-demo) + User `test@test.nl` / `test1234`, role: `owner`
- `database/seeders/DatabaseSeeder.php` — updated to call `LocalAdminSeeder` + `LocalCompanySeeder` when `app()->environment('local')`

---

## Verification

```
php artisan migrate:fresh --seed

ModuleCatalogSeeder   ✅
LocalAdminSeeder      ✅  (test@test.nl / test1234 — Admin, super_admin)
LocalCompanySeeder    ✅  (FlowFlex Demo company + test@test.nl / test1234 — User, owner role)
```

All 10 migrations pass. Seeders complete without errors.

---

## Login Credentials (local only)

| Panel | URL | Email | Password | Role |
|---|---|---|---|---|
| Admin | `/admin/login` | `test@test.nl` | `test1234` | super_admin |
| Workspace | `/app/login` | `test@test.nl` | `test1234` | owner |

---

## Docker Services

| Service | Container | Accessible At |
|---|---|---|
| Nginx | flowflex_nginx | http://localhost:8080 |
| PostgreSQL | flowflex_postgres | localhost:5432 |
| Redis | flowflex_redis | localhost:6379 |
| Mailpit UI | flowflex_mailpit | http://localhost:8025 |
| Mailpit SMTP | flowflex_mailpit | localhost:1025 |
| Reverb WS | flowflex_reverb | ws://localhost:6001 |

---

## Notes

- `.env` local DB host must be `postgres` (Docker service name) when running inside Docker, `127.0.0.1` when running artisan locally
- `setPermissionsTeamId($company->id)` called in `LocalCompanySeeder` before `assignRole()` — required for Spatie teams mode
- Telescope navigation item uses `->visible(fn () => app()->environment('local'))` — hidden in non-local envs
- Horizon/Pulse are protected by gates, NOT by admin panel auth — they run on their own routes outside Filament

---

## Related Left-Brain Specs

- [[docker-local-environment]]
- [[admin-panel-flowflex]]
- [[project-scaffolding]]
