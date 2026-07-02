---
domain: core
module: health-monitoring
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Health Monitoring — Architecture

Parent: [[_module]] · See also [[security]]

Configuration-only: no services, DTOs, jobs, or state machines. Everything is wiring in `AppServiceProvider` and config.

## Health checks

Registered in `AppServiceProvider` via `spatie/laravel-health`:

```php
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

| Check | Watches |
|---|---|
| DatabaseCheck | PostgreSQL reachable |
| RedisCheck | Redis reachable |
| MeilisearchCheck | search engine reachable |
| HorizonCheck | Horizon running |
| UsedDiskSpaceCheck | disk usage, warn >70% |
| QueueCheck ×2 | depth of `domain-events`, `notifications` |
| EnvironmentCheck | expects `production` |

## Pulse / Horizon / Sentry

- **Pulse** (`/pulse`, config `config/pulse.php`): slow queries (>100ms), exception rate, queue throughput/depth, cache hit/miss, server resources.
- **Horizon** (`/horizon`): queue depth, throughput, failed jobs with stack trace, worker count. See [[../../../architecture/queue-jobs]].
- **Sentry** (`config/sentry.php`): all production exceptions captured with `company_id` and `user_id` tags.

## Surfaces

- `SystemStatusPage` (`/app`, custom Filament page + `system-status.blade.php`): green/red per check, last-checked timestamp, polling 60s.
- Pulse + Horizon are external dashboards linked from `/admin` nav, gated by the admin guard. See [[../../../infrastructure/deployment]].
