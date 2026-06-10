---
type: module
domain: Foundation
domain-key: foundation
panel: (scaffold)
module-key: foundation.docker
status: planned
priority: v1-core
depends-on: [foundation.scaffold]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [dev]
tables: []
permission-prefix: ""
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Docker Environment

Docker Compose local development stack — `docker compose up` starts all eight services. Mailpit captures email, Horizon processes queued jobs, Reverb handles WebSockets, Meilisearch serves search. Local seeders create ready-to-use admin and tenant accounts.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/laravel-scaffold\|foundation.scaffold]] | a Laravel project must exist to containerise |

---

## Core Features

- Eight services: `app` (PHP 8.4 FPM), `nginx` (port 8080), `postgres` (PostgreSQL 17), `redis` (Redis 8), `meilisearch` (port 7700), `mailpit` (SMTP capture + UI port 8025), `horizon` (queue worker), `reverb` (WebSocket server)
- Custom PHP 8.4 FPM Dockerfile with extensions: `pdo_pgsql`, `redis`, `pcntl`, `bcmath`, `intl`, `zip`, `gd`
- `LocalAdminSeeder`: creates `admin@flowflex.nl` / `password` with role `super_admin`
- `LocalCompanySeeder`: creates company "FlowFlex Demo" + owner user `demo@flowflex.nl` / `password`
- `docker compose watch` for hot reload of PHP files without container restart
- Horizon dashboard at `/horizon`, Pulse at `/pulse`, Telescope at `/telescope` (dev only)
- Mailpit web UI at `localhost:8025`

---

## Service Table

| Service | Image | Port | Purpose |
|---|---|---|---|
| `app` | Custom PHP 8.4 FPM | — | Laravel application |
| `nginx` | nginx:alpine | 8080→80 | Reverse proxy |
| `postgres` | postgres:17 | 5432 | Primary database |
| `redis` | redis:8-alpine | 6379 | Cache, queues, sessions |
| `meilisearch` | getmeili/meilisearch:v1 | 7700 | Full-text search |
| `mailpit` | axllent/mailpit | 8025, 1025 | Email capture |
| `horizon` | Same as app | — | Queue worker |
| `reverb` | Same as app | proxied | WebSocket server |

Full .env spec, daily commands, and troubleshooting: [[architecture/local-dev]].

---

## Common Commands

```bash
docker exec flowflex_app php artisan migrate
docker exec flowflex_app php artisan migrate --seed
docker exec flowflex_app php artisan test
docker exec flowflex_app php artisan typescript:transform
docker exec flowflex_app php artisan queue:restart
```

---

## DTOs / Services & Actions / Filament / Permissions

None — local dev infrastructure only.

---

## Test Checklist

- [ ] `docker compose up -d` brings all eight services healthy
- [ ] `docker exec flowflex_app php artisan migrate --seed` runs clean
- [ ] App reachable at `localhost:8080`; Mailpit at `:8025`; Meilisearch at `:7700`
- [ ] Horizon dashboard shows supervisors running
- [ ] `docker compose watch` reloads a touched PHP file
- [ ] Test email lands in Mailpit (tinker `Mail::raw(...)`)

---

## Build Manifest

```
docker-compose.yml
docker/php/Dockerfile
docker/nginx/default.conf
docker/php/php.ini (memory, upload limits)
.dockerignore
```

(Seeders themselves: [[domains/foundation/permissions-seed]].)

---

## Related

- [[domains/foundation/laravel-scaffold]]
- [[architecture/local-dev]]
- [[architecture/tech-stack]]
