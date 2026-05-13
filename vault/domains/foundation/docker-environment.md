---
type: module
domain: Foundation
panel: (scaffold — no panel)
module-key: foundation.docker
status: complete
color: "#4ADE80"
last_updated: 2026-05-13
right_brain_log: "[[builder-log-phase-0-foundation]]"
---

# Docker Environment

> Docker Compose local development stack: nginx, PHP 8.4 FPM, PostgreSQL 17, Redis 8, Mailpit, Horizon worker, and Reverb WebSocket server — `docker compose up` starts everything.

**Domain:** Foundation
**Module key:** `foundation.docker`

## What It Does

The Docker environment provides a reproducible local development stack for FlowFlex. A single `docker compose up` command starts all seven services. Mailpit captures outgoing email so no real emails are sent during development. Horizon runs as a separate service so queued jobs process automatically without a manual `artisan queue:work`. Reverb runs as a separate service so WebSocket broadcasting works locally. Local seeders create ready-to-use admin and tenant accounts so developers can log in immediately after `docker compose up && php artisan migrate --seed`.

## Features

### Core
- Seven Docker Compose services: `app` (PHP 8.4 FPM), `nginx` (reverse proxy, port 8080), `postgres` (PostgreSQL 17, port 5432), `redis` (Redis 8 Alpine, port 6379), `mailpit` (SMTP capture + UI port 8025), `horizon` (queue worker), `reverb` (WebSocket server)
- Custom PHP 8.4 FPM Dockerfile with all required extensions: `pdo_pgsql`, `redis`, `pcntl`, `bcmath`, `intl`, `zip`, `gd`
- `LocalAdminSeeder`: creates admin `test@test.nl` / `test1234` with role `super_admin`
- `LocalCompanySeeder`: creates company "FlowFlex Demo" + tenant user `test@test.nl` / `test1234` with role `owner`
- Both local seeders run only when `app()->environment('local')`

### Advanced
- Nginx config proxies all requests to PHP-FPM; WebSocket upgrade handled for Reverb
- Volume mounts: app code at `/var/www/html`, PostgreSQL data persisted in named volume `postgres_data`, Redis data in `redis_data`
- Horizon and Reverb containers share the same image as `app` — started with different `command` overrides
- Mailpit web UI at `localhost:8025` — browse captured emails during development
- Laravel Horizon dashboard at `/horizon` (admin panel guard only), Pulse at `/pulse`, Telescope at `/telescope` (dev env only)

### AI-Powered
- Health check endpoints configured on all services — Docker Compose `healthcheck` prevents dependent services from starting before their dependencies are ready
- `docker compose watch` sync configured for hot reload of PHP files without restarting containers

## Data Model

```erDiagram
    docker_services {
        string name PK
        string image
        string purpose
        string port_mapping
    }
```

| Service | Image | Port | Purpose |
|---|---|---|---|
| `app` | Custom PHP 8.4 FPM | — | Laravel application server |
| `nginx` | nginx:alpine | 8080→80 | Reverse proxy |
| `postgres` | postgres:17 | 5432 | Primary database |
| `redis` | redis:8-alpine | 6379 | Cache, queues, sessions |
| `mailpit` | axllent/mailpit | 8025, 1025 | Email capture |
| `horizon` | Same as app | — | Queue worker |
| `reverb` | Same as app | 8080 (proxied) | WebSocket server |

## Permissions

- `foundation.docker.start`
- `foundation.docker.stop`
- `foundation.docker.seed`
- `foundation.docker.logs`
- `foundation.docker.configure`

## Filament

- **Resource:** None (local-dev tooling, no UI)
- **Pages:** None
- **Custom pages:** None
- **Widgets:** None
- **Nav group:** N/A

## Related

- [[laravel-scaffold]]
- [[filament-panels]]
- [[test-suite]]
