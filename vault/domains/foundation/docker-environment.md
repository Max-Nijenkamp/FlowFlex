---
type: module
domain: Foundation
panel: (scaffold)
module-key: foundation.docker
status: planned
color: "#4ADE80"
---

# Docker Environment

Docker Compose local development stack — `docker compose up` starts all seven services. Mailpit captures email, Horizon processes queued jobs, Reverb handles WebSockets. Local seeders create ready-to-use admin and tenant accounts.

---

## Core Features

- Seven services: `app` (PHP 8.4 FPM), `nginx` (port 8080), `postgres` (PostgreSQL 17), `redis` (Redis 8), `mailpit` (SMTP capture + UI port 8025), `horizon` (queue worker), `reverb` (WebSocket server)
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
| `mailpit` | axllent/mailpit | 8025, 1025 | Email capture |
| `horizon` | Same as app | — | Queue worker |
| `reverb` | Same as app | proxied | WebSocket server |

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

## Filament

No Filament resources — local dev tooling only.

---

## Related

- [[domains/foundation/laravel-scaffold]]
- [[architecture/tech-stack]]
