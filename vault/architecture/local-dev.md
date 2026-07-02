---
type: architecture
category: infra
pattern-key: dev
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Local Development

> [!warning] Authoritative infra source moved
> Verified infrastructure facts now live in [[../infrastructure/docker-stack]] + [[../infrastructure/secrets-env]]. Details on this page may predate the 2026-06-20 rebuild — trust the linked note on any conflict.


Everything to get a working dev environment and stay unblocked: .env spec, Docker service map, daily commands, and a troubleshooting table for the usual suspects.

---

## Docker Services (local)

`docker compose up` — nine services. Container name prefix `flowflex_`.

| Service | Image | Host port | Purpose |
|---|---|---|---|
| `app` | custom PHP 8.4 FPM | — | Laravel; extensions: `pdo_pgsql`, `redis`, `pcntl`, `bcmath`, `intl`, `zip`, `gd` |
| `nginx` | nginx:alpine | 8080 | reverse proxy → app |
| `postgres` | postgres:17 | 5432 | primary DB |
| `redis` | redis:8-alpine | 6379 | cache, queues, sessions |
| `meilisearch` | getmeili/meilisearch:v1 | 7700 | full-text search |
| `mailpit` | axllent/mailpit | 8025 (UI), 1025 (SMTP) | email capture |
| `horizon` | same as app | — | queue worker (`php artisan horizon`) |
| `scheduler` | same as app | — | `php artisan schedule:work` — runs the Laravel schedule (added 2026-06-14) |
| `reverb` | same as app | proxied via nginx | WebSocket server (`php artisan reverb:start`) |

**The `scheduler` container is required for the health/status pages.** Health checks (`RunHealthChecksCommand`) and the queue heartbeat (`DispatchQueueCheckJobsCommand`) run on the scheduler every minute; without it the SystemStatusPage / public `/status` show stale or failing checks. The **QueueCheck specifically needs the heartbeat dispatcher scheduled** — it measures when a heartbeat job was last *processed* per queue, so `RunHealthChecks` alone reports `Queue: failed` forever. Both are in `routes/console.php`.

Dashboards: app `localhost:8080` · Horizon `/horizon` · Pulse `/pulse` · Telescope `/telescope` (dev only) · Mailpit `localhost:8025` · Meilisearch `localhost:7700`.

Seeded logins (LocalDevSeeder): `admin@flowflex.nl` / `password` (super_admin, `/admin`) · `demo@flowflex.nl` / `password` (owner of "FlowFlex Demo", `/app`).

---

## .env (local) — Full Spec

Every variable with its dev default. Production values: [[architecture/deployment]].

```env
# App
APP_NAME="FlowFlex"
APP_ENV=local
APP_KEY=                      # php artisan key:generate
APP_URL=http://localhost:8080
APP_DEBUG=true
APP_LOCALE=en
APP_TIMEZONE=UTC              # always UTC — per-company TZ handled in app layer

# Database
DB_CONNECTION=pgsql
DB_HOST=postgres              # docker service name
DB_PORT=5432
DB_DATABASE=flowflex
DB_USERNAME=flowflex
DB_PASSWORD=secret

# Redis (separate DBs per concern — mirrors production)
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3
REDIS_RATE_LIMIT_DB=4

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false   # true in production

# Queue
QUEUE_CONNECTION=redis
HORIZON_PREFIX=flowflex_local

# Meilisearch
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=masterKey     # dev master key, set in compose
MEILISEARCH_INDEX_PREFIX=flowflex_local_

# Mail → Mailpit
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=no-reply@flowflex.test
MAIL_FROM_NAME="FlowFlex (dev)"

# Stripe — TEST MODE keys only in local/staging
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...   # from `stripe listen --forward-to localhost:8080/webhooks/stripe`

# Reverb
REVERB_APP_ID=flowflex
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080

# Files — local disk in dev (R2 in production)
FILESYSTEM_DISK=local

# Monitoring
TELESCOPE_ENABLED=true
PULSE_ENABLED=true

# Logging
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug
```

Tests use SQLite in-memory regardless of the above (`phpunit.xml` overrides `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) — see [[architecture/patterns/testing-pattern]].

---

## Daily Commands

```bash
docker compose up -d                                   # start stack
docker compose watch                                   # hot reload PHP
docker exec flowflex_app php artisan migrate --seed    # fresh start
docker exec flowflex_app php artisan test              # Pest
docker exec flowflex_app ./vendor/bin/pint --dirty     # style
docker exec flowflex_app ./vendor/bin/phpstan analyse  # static analysis
docker exec flowflex_app php artisan typescript:transform  # after DTO changes
npm run dev                                            # Vite (host machine)
```

---

## Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Jobs never process | Horizon container down or crashed after code change | `docker compose restart horizon`; after deploy-like changes `php artisan queue:restart` |
| `MissingCompanyContextException` in a job | Listener missing `WithCompanyContext` middleware or event lacks `company_id` | Add middleware + scalar `company_id` to event ([[architecture/multi-tenancy]]) |
| WebSocket not connecting | Reverb container down, or `VITE_REVERB_*` baked stale into assets | restart reverb; re-run `npm run dev` after env change |
| Search returns nothing | Index never built or prefix mismatch | `php artisan scout:sync-index-settings` then `php artisan scout:import "App\Models\..."` |
| Email never arrives | Looking at real inbox instead of Mailpit | open `localhost:8025` |
| Stripe webhooks silent | No forwarder running | `stripe listen --forward-to localhost:8080/webhooks/stripe`, copy whsec into .env |
| `permission denied` on storage/ | Volume owned by root | `docker exec flowflex_app chown -R www-data:www-data storage bootstrap/cache` |
| Filament page blank | static `$view` property (Filament 4 style) | instance property — [[architecture/filament-patterns]] #2 |
| Resource invisible in nav | `canAccess()` false (module not active for demo company) or namespace mismatch in `discoverResources` | activate module via marketplace seeder; check namespace |
| Vite theme changes ignored | panel theme css not registered in `vite.config.js` | add to `input:` array — [[architecture/filament-patterns]] #6 |
| **PHP edit gives a 500 / stale parse error in browser but `php -l` is clean** | opcache in the `app` container caches the old bytecode | `docker exec flowflex_app php artisan optimize:clear` (or `docker compose restart app`). Edits land on disk via volume mount; opcache is what's stale |
| **Skin/CSS change "doesn't apply"** | served `theme-*.css` is the old build hash, or browser cached it | rebuild (`npm run build`), `optimize:clear`, hard-refresh (⌘⇧R); compare `public/build/assets/theme-*` hash to the page's `<link>` |
| **`/status` or SystemStatusPage shows failed/stale checks** | `scheduler` container down, or QueueCheck has no heartbeat | `docker compose up -d scheduler`; confirm `DispatchQueueCheckJobsCommand` is scheduled |
| Filament health `Queue: failed` despite Horizon up | QueueCheck needs heartbeat jobs *processed*, not just RunHealthChecks | schedule `DispatchQueueCheckJobsCommand` every minute |
| Port 5432/6379/7700 conflict | host service already running | stop host service or remap compose ports |

---

## Related

- [[domains/foundation/docker-environment/_module]] — the module that builds this stack
- [[architecture/deployment]] — production env vars
- [[architecture/patterns/testing-pattern]] — SQLite test config
- [[architecture/patterns/seeders]] — LocalDevSeeder contents

---

## Two Databases — Know Which One You're Hitting

| Surface | Database | Seed/migrate with |
|---|---|---|
| Browser (nginx `:8080`) | docker pgsql (`flowflex_postgres`) | `docker compose exec -T app php artisan migrate:fresh --seed --force` |
| Host CLI (`php artisan ...`) | local sqlite `database/database.sqlite` | `php artisan migrate:fresh --seed` |
| Test suite | sqlite `:memory:` | automatic (RefreshDatabase) |

Host-side seeds never reach the browser's database. After changing migrations, run the pgsql gate (way-of-working) — sqlite tolerates constraint orderings pgsql rejects.

---

## Quick-Test Logins (LocalDevSeeder — never runs in production)

| Surface | Email | Password | Scope |
|---|---|---|---|
| `/admin` staff console | test@test.nl | test1234 | super_admin |
| All tenant panels | test@test.nl | test1234 | FlowFlex Demo owner — every permission, every module active |
| `/admin` staff console | admin@flowflex.nl | password | super_admin |
| All tenant panels | demo@flowflex.nl | password | FlowFlex Demo owner |

---

## Host CLI Quirks (2026-06-12)

- **PHP memory limit**: host php defaults to 128M — pint/phpstan/pest all crash. Always: `php -d memory_limit=1G vendor/bin/pest`, `vendor/bin/phpstan analyse --memory-limit=1G`.
- **Stale spatie permission cache**: after reseeding the docker DB, run `docker compose exec -T app php artisan permission:cache-reset` — Redis keeps the old permission map and produces phantom 403s.
- **pgsql vs sqlite blind spots** (suite is sqlite — these only break in the browser):
  - JSON operators: columns queried with `->>` (e.g. Filament's notifications bell on `data`) must be `jsonb`, not `text`
  - Self-referencing FKs inside `Schema::create` — declare via post-create `Schema::table` alters
  - SQL date functions diverge — group dates in PHP (widgets/charts)
  - After ANY migration change: run the pgsql gate (`docker compose exec -T app php artisan migrate:fresh --seed --force`)
