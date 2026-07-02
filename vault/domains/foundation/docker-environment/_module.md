---
domain: foundation
module: docker-environment
type: module
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Docker Environment

`foundation.docker` — the local-dev Compose stack. `docker compose up` starts **9** services. This note is the spec-level view; the authoritative, line-by-line infra truth lives in [[../../../infrastructure/docker-stack]] — defer there rather than duplicate.

> [!note] Corrected from flat spec (verified against repo-root `docker-compose.yml`)
> The old spec was the most error-laden in the domain. Fixes applied:
> - **9 services, not 8** — `scheduler` (`php artisan schedule:work`) was missing.
> - Only **nginx `8080:80`** and **postgres `5432:5432`** are host-published. `redis`, `mailpit`, `reverb` are internal/`expose` (host ports already saturated).
> - **Reverb runs `--port=8081`**, not 8080.
> - **Redis uses `--requirepass secret`**, not null/no-auth.
> - Seeders = a single **`LocalDevSeeder`** (not `LocalAdminSeeder`/`LocalCompanySeeder`). See [[../permissions-seed/_module|permissions-seed]] for the logins it creates.

## Services (9)

| Service | Image / command | Host port | Internal |
|---|---|---|---|
| `app` | custom PHP-FPM (`docker/php`) | — | fpm 9000 |
| `nginx` | nginx:alpine | **8080→80** | |
| `postgres` | postgres:17 | **5432→5432** | health: `pg_isready` |
| `redis` | redis:8-alpine `--requirepass secret` | — | `redis:6379` |
| `meilisearch` | getmeili/meilisearch:**v1.10** | 7700→7700 | |
| `mailpit` | axllent/mailpit | — (`expose 1025,8025`) | `mailpit:1025` |
| `horizon` | app image, `php artisan horizon` | — | |
| `scheduler` | app image, `php artisan schedule:work` | — | health/queue heartbeat |
| `reverb` | app image, `reverb:start --port=8081` | — (`expose 8081`) | |

Full env block, healthchecks, volumes, watch config, and the "why ports are unpublished" notes: [[../../../infrastructure/docker-stack]].

## Common Commands

```bash
docker exec flowflex_app php artisan migrate --seed
docker exec flowflex_app php artisan test
docker compose exec mailpit ...   # inspect captured mail (8025 not published)
```

## Test Checklist

- [x] `docker compose up -d` brings all 9 services up (postgres/redis healthchecks gate `app`)
- [x] `migrate --seed` runs clean (M0 exit gate)
- [x] App reachable at `localhost:8080`
- [ ] Mailpit/Reverb only reachable internally (publish a free host port for browser work)

No DTOs / Services / Filament / Permissions — local-dev infrastructure only.

## Related

- [[../../../infrastructure/docker-stack]] — authoritative infra spec
- [[../laravel-scaffold/_module|Laravel Scaffold]]
- [[../permissions-seed/_module|Permissions Seeder]] — the seed logins
- [[../../../architecture/local-dev]]
