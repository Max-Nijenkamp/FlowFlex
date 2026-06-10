---
type: architecture
category: infra
pattern-key: deployment
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Deployment & Production

Production environment, environment variables, health checks, and what "production-ready" means for FlowFlex.

---

## Production Stack

```
Internet → Cloudflare (CDN + DDoS) → Nginx (TLS termination + proxy)
                                         ↓
                                   PHP-FPM (Laravel 13)
                                         ↓
                             ┌───────────┬────────────────┐
                         PostgreSQL 17  Redis 8      Meilisearch 1.x
                         (primary DB)  (cache/queue)  (search)
                                         ↓
                               Laravel Horizon (workers)
                               Laravel Reverb (WebSocket)
```

Storage: Cloudflare R2 (S3-compatible) for all file uploads. Emails: Resend or Postmark (transactional), Mailchimp (marketing).

---

## Required Environment Variables

```env
# App
APP_NAME="FlowFlex"
APP_ENV=production
APP_KEY=base64:...
APP_URL=https://app.flowflex.io
APP_DEBUG=false

# Database
DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=flowflex_prod
DB_USERNAME=...
DB_PASSWORD=...

# Redis (separate DBs per concern)
REDIS_HOST=...
REDIS_PASSWORD=...
REDIS_PORT=6379
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3
REDIS_RATE_LIMIT_DB=4

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

# Queue
QUEUE_CONNECTION=redis
HORIZON_PREFIX=flowflex

# Meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=...
MEILISEARCH_INDEX_PREFIX=flowflex_prod_

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=587
MAIL_USERNAME=resend
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=no-reply@flowflex.io
MAIL_FROM_NAME="FlowFlex"

# Stripe
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Reverb (WebSocket)
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https

# Vite (compiled into frontend assets)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${APP_URL}"
VITE_REVERB_PORT=443

# S3 / Cloudflare R2
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=auto
AWS_BUCKET=flowflex-uploads
AWS_URL=https://...r2.cloudflarestorage.com
AWS_ENDPOINT=https://...r2.cloudflarestorage.com

# Monitoring
TELESCOPE_ENABLED=false    # dev only
PULSE_ENABLED=true

# Logging
LOG_CHANNEL=stack
LOG_STACK=single,slack
LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/...
LOG_LEVEL=warning          # production: warning|error only
```

---

## Health Checks

`spatie/laravel-health` registers health checks at `GET /health`:

```php
// app/Providers/AppServiceProvider.php
Health::checks([
    DatabaseCheck::new(),
    RedisCheck::new(),
    MeilisearchCheck::new(),
    HorizonCheck::new(),
    UsedDiskSpaceCheck::new()->warnWhenUsedSpaceIsAbovePercentage(70),
    QueueCheck::new()->onQueue('domain-events'),
    EnvironmentCheck::new()->expectEnvironment('production'),
]);
```

`GET /health` returns JSON with status per check. Used by uptime monitors (Better Uptime, Pulsetic) and deployment health checks.

`GET /health/pulse` → Laravel Pulse metrics dashboard (authenticated, admin only).

---

## Deployment Checklist

Before every production deploy:

```bash
# 1. Put in maintenance mode (returns 503)
php artisan down --retry=60 --secret=deploy-token-xyz

# 2. Pull latest code
git pull origin main

# 3. Install/update dependencies
composer install --no-dev --optimize-autoloader

# 4. Run migrations
php artisan migrate --force

# 5. Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Restart queue workers (picks up new code)
php artisan queue:restart

# 7. Rebuild search indexes if models changed
php artisan scout:sync-index-settings

# 8. Bring back up
php artisan up
```

---

## Zero-Downtime Deployment

Use Laravel Octane + deployment with Caddy or a rolling deploy strategy. Alternatively, a simple blue/green approach:

1. Spin up new container with new code
2. Run migrations against shared DB (must be backward-compatible)
3. Health check passes on new container
4. Swap Nginx upstream to new container
5. Stop old container

**Migration safety rule**: never remove a column or rename a table in the same deploy as the code that stops using it. Two-step: deploy code that handles both old and new schema → migrate → deploy code that removes the old fallback.

### Zero-Downtime Migration Checklist

Run through this for every production migration (expand–contract pattern):

- [ ] **Additive only in deploy N**: new columns nullable or defaulted; new tables; new indexes
- [ ] **Indexes on big tables**: `CREATE INDEX CONCURRENTLY` (raw statement, `withinTransaction = false` on the migration) — plain `CREATE INDEX` locks writes
- [ ] **Column drops/renames**: deploy N stops reading/writing old name → deploy N+1 drops it. Rename = add new + backfill + dual-write window + drop old (never `RENAME COLUMN` on hot tables)
- [ ] **Type changes**: new column + backfill + swap reads + drop, never in-place `ALTER TYPE` on large tables
- [ ] **Backfills**: chunked command on the `default` queue, not inside the migration (migrations must run in seconds)
- [ ] **NOT NULL on existing column**: backfill first, add `CHECK` constraint `NOT VALID` → `VALIDATE CONSTRAINT`, then set NOT NULL
- [ ] **Queued jobs survive the deploy**: old workers may process jobs referencing the old schema for up to `timeout` seconds — code in deploy N must tolerate both schemas
- [ ] Down-migrations exist and are tested for the last 3 migrations (rollback path)

---

## Monitoring & Alerts

| Tool | Monitors | Alert Channel |
|---|---|---|
| Laravel Pulse | Slow queries, exceptions, queue depth | Dashboard |
| Laravel Horizon | Failed jobs, queue throughput | Slack `#platform-alerts` |
| `spatie/laravel-health` | DB, Redis, Meili, disk | PagerDuty / Slack |
| Uptime monitor | `/health` endpoint | PagerDuty |
| Cloudflare | DDoS, error rate spike | Email |
| Stripe dashboard | Payment failures, dispute rate | Email |

Log channel in production: `stack` with `single` (file) + `slack` (warning+). Sentry can be added for error tracking (`sentry/sentry-laravel`).

---

## Backups

- **PostgreSQL**: automated daily backup via managed DB provider (DigitalOcean Managed DB, Supabase, or RDS). Point-in-time recovery with 7-day retention.
- **Redis**: persistence enabled (`appendonly yes`). Backup via Redis RDB snapshot daily.
- **Cloudflare R2**: replication to secondary bucket weekly.
- **Test restores**: run quarterly — a backup not tested is not a backup.

---

## Staging Environment

Staging mirrors production except:
- `APP_ENV=staging`
- `MEILISEARCH_INDEX_PREFIX=flowflex_staging_`
- `STRIPE_KEY` → Stripe test mode keys
- `LOG_LEVEL=debug`
- Separate PostgreSQL database (never point staging at prod DB)
- Telescope enabled (`TELESCOPE_ENABLED=true`)
