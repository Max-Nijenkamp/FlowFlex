---
domain: foundation
module: docker-environment
type: module
build-status: in-progress
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Docker Environment

`foundation.docker` — the local-dev Compose stack. `docker compose up` starts **9** services. This note is the spec-level view; the authoritative, line-by-line infra truth lives in [[../../../infrastructure/docker-stack]] — defer there rather than duplicate.

## Module-key

`foundation.docker`

**Priority:** v1-core (M0)  
**Panel:** none (local-dev infrastructure)  
**Permission prefix:** none  
**Tables:** none (containerises the runtime; owns no application tables)

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../laravel-scaffold/_module\|foundation.scaffold]] | Containerises the scaffold's runtime |

## Core Features

- 9-service Compose stack: app · nginx · postgres · redis · meilisearch · mailpit · horizon · scheduler · reverb — see [[./features/dev-stack|Local Dev Stack]]
- One-command bring-up; postgres/redis healthchecks gate `app` start
- Only `nginx 8080` + `postgres 5432` host-published; redis / mailpit / reverb internal-only
- `migrate --seed` → working demo logins ([[../permissions-seed/_module|permissions-seed]])

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

- [ ] Tenant isolation: n/a — local-dev infrastructure owns no tenant data (isolation lives in [[../multi-tenancy-layer/_module|multi-tenancy-layer]])
- [ ] Module gating: n/a — `foundation.docker` is always-on local-dev infra, not a billable/gateable module
- [x] `docker compose up -d` brings all 9 services up (postgres/redis healthchecks gate `app`)
- [x] `migrate` runs clean against container pgsql (verified rebuild 2026-07-03); `--seed` completes the M0 gate once [[../permissions-seed/_module|permissions-seed]] ships LocalDevSeeder
- [x] App reachable at `localhost:8080`
- [x] Mailpit/Reverb only reachable internally (verified 2026-07-03 — host :8081 unreachable; publish a free port temporarily for browser work)

No DTOs / Services / Filament / Permissions — local-dev infrastructure only.

## Related

- [[../../../infrastructure/docker-stack]] — authoritative infra spec
- [[../laravel-scaffold/_module|Laravel Scaffold]]
- [[../permissions-seed/_module|Permissions Seeder]] — the seed logins
- [[../../../architecture/local-dev]]
