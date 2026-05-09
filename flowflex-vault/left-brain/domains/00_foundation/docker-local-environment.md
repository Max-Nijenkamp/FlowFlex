---
type: module
domain: Foundation
panel: n/a
phase: 0
status: complete
last_updated: 2026-05-09
right_brain_log: "[[builder-log-docker-local-environment]]"
---

# Docker & Local Environment

Local development environment for FlowFlex using Docker Compose. Runs the full stack: PHP 8.4 FPM, Nginx, PostgreSQL 17, Redis 8, Mailpit (email capture), Laravel Horizon (queue worker), and Laravel Reverb (WebSocket server).

No production Docker setup — this module is local-only. Production deployment strategy is out of scope for Phase 0.

---

## Services

| Service | Image | Purpose | Port |
|---|---|---|---|
| `app` | Custom PHP 8.4 FPM | Laravel PHP-FPM process | — |
| `nginx` | nginx:alpine | Reverse proxy / web server | `8080` → `80` |
| `postgres` | postgres:17 | Primary database | `5432` |
| `redis` | redis:8-alpine | Cache, queues, sessions | `6379` |
| `mailpit` | axllent/mailpit | Local SMTP capture + web UI | `8025` (UI), `1025` (SMTP) |
| `horizon` | Same as `app` | Runs `php artisan horizon` | — |
| `reverb` | Same as `app` | Runs `php artisan reverb:start` | `8080` (proxied) |

---

## Monitoring — Admin Panel Integration

The following Laravel packages are installed and accessible via the admin panel:

| Tool | URL | Panel Integration | Access |
|---|---|---|---|
| **Laravel Horizon** | `/horizon` | Navigation link in admin panel | Admin guard only |
| **Laravel Pulse** | `/pulse` | Navigation link in admin panel | Admin guard only |
| **Laravel Telescope** | `/telescope` | Navigation link (dev env only) | Admin guard only |

All three tools are protected by an `AdminAccessGate` that only allows authenticated `Admin` model users (the `admin` guard) — they are not accessible to tenant users.

---

## Local Seeders

When `APP_ENV=local`, the database seeder creates test accounts for quick login:

| Seeder | What It Creates |
|---|---|
| `LocalAdminSeeder` | Admin user `test@test.nl` / `test1234` — role: `super_admin` |
| `LocalCompanySeeder` | Company "FlowFlex Demo" + tenant user `test@test.nl` / `test1234` — role: `owner` |

The `DatabaseSeeder` calls both local seeders only when `app()->environment('local')`.

---

## .env Local Defaults

```dotenv
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=flowflex
DB_USERNAME=flowflex
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@flowflex.local"
MAIL_FROM_NAME="FlowFlex"

QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
CACHE_STORE=redis

REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

---

## Directory Structure

```
app/
├── Dockerfile
├── docker-compose.yml
├── docker/
│   └── nginx/
│       └── default.conf
└── database/
    └── seeders/
        ├── LocalAdminSeeder.php
        └── LocalCompanySeeder.php
```

---

## Features

- Single `docker compose up` starts full stack
- Mailpit captures all outgoing email — no real emails sent locally
- Horizon runs as a separate Docker service — queued jobs process automatically
- Reverb runs as a separate Docker service — WebSockets work in local
- Admin panel shows links to Horizon, Pulse, and Telescope dashboards
- Local seeders: admin `test@test.nl`/`test1234` + company + tenant user with same credentials

---

## Related

- [[MOC_Foundation]]
- [[project-scaffolding]]
- [[admin-panel-flowflex]]
