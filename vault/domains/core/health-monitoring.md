---
type: module
domain: Core Platform
panel: app
module-key: core.health
status: planned
color: "#4ADE80"
---

# Health Monitoring

System health checks, Laravel Pulse metrics dashboard, and Horizon queue monitoring — accessible to company admins and FlowFlex staff.

---

## Core Features

- `GET /health` — JSON health check endpoint via `spatie/laravel-health`:
  - Database connectivity
  - Redis connectivity
  - Meilisearch connectivity
  - Horizon running
  - Queue depth (`domain-events`, `notifications`)
  - Disk space (warn >70%)
  - Environment check (production vs expected)
- Laravel Pulse dashboard at `/pulse` — authenticated admin-only:
  - Slow queries (>100ms)
  - Exception rate
  - Queue throughput and depth
  - Cache hit/miss ratio
  - Server resource usage (CPU, memory)
- Laravel Horizon dashboard at `/horizon` — admin guard only (FlowFlex staff + company owner):
  - Queue depth per queue
  - Job throughput
  - Failed jobs with stack trace
  - Worker process count
- Sentry integration: all exceptions in production captured with `company_id` and `user_id` tags

---

## Data Model

No additional tables — all health data is ephemeral (Redis-backed Pulse, Horizon).

---

## Filament

**`/app` panel (Company Owner):**
- `SystemStatusPage` (custom page) — simplified health view: all green / any red indicator with last-checked timestamp
- Links to Horizon (if owner) and Pulse

**`/admin` panel (FlowFlex staff):**
- Full Pulse + Horizon dashboards embedded
- Cross-company exception view via Sentry

---

## Configuration

```php
// app/Providers/AppServiceProvider.php
Health::checks([
    DatabaseCheck::new(),
    RedisCheck::new(),
    MeilisearchCheck::new(),
    HorizonCheck::new(),
    UsedDiskSpaceCheck::new()->warnWhenUsedSpaceIsAbovePercentage(70),
    QueueCheck::new()->onQueue('domain-events'),
    QueueCheck::new()->onQueue('notifications'),
    EnvironmentCheck::new()->expectEnvironment('production'),
]);
```

---

## Related

- [[architecture/deployment]]
- [[architecture/queue-jobs]]
- [[architecture/packages]] (`spatie/laravel-health`, `sentry/sentry-laravel`)
