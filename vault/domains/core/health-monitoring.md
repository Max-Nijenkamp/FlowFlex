---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.health
status: planned
priority: v1
depends-on: [foundation.queues, foundation.panels]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: core.health
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Health Monitoring

System health checks, Laravel Pulse metrics dashboard, and Horizon queue monitoring — accessible to company owners (simplified) and FlowFlex staff (full).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/queue-workers\|foundation.queues]] | Horizon checks/dashboard |
| Hard | [[domains/foundation/filament-panels\|foundation.panels]] | status page in `/app`, full dashboards via `/admin` |

---

## Core Features

- `GET /health` — JSON health check endpoint via `spatie/laravel-health`: database, Redis, Meilisearch, Horizon running, queue depth (`domain-events`, `notifications`), disk space (warn >70%), environment check
- Laravel Pulse dashboard at `/pulse` — authenticated admin-only: slow queries (>100ms), exception rate, queue throughput/depth, cache hit/miss, server resources
- Laravel Horizon dashboard at `/horizon` — admin guard only: queue depth, throughput, failed jobs with stack trace, worker count
- Sentry integration: all production exceptions captured with `company_id` and `user_id` tags

---

## Data Model

No additional tables — all health data is ephemeral (Redis-backed Pulse, Horizon).

## DTOs / Services & Actions

None — configuration module. Health checks registered in `AppServiceProvider`:

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

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SystemStatusPage` (`/app`, owner) | #7 custom page | green/red per check, last-checked timestamp, polling 60s |
| Pulse + Horizon (`/admin`) | external dashboards | gated by admin guard; links from admin nav |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('core.health.view-any') && BillingService::hasModule('core.health')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a throttle limiter on GET /health and/or restrict detailed output to authenticated/monitoring callers (token-guarded), returning minimal status to anonymous callers.

---

## Permissions

`core.health.view` — owner only in `/app`. `/pulse` + `/horizon` gates: admin guard (+ owner for Horizon read *(assumed: staff-only in v1, simpler)*).

---

## Test Checklist

- [ ] `GET /health` returns JSON with all registered checks
- [ ] `/horizon` + `/pulse` inaccessible to tenant non-owner users
- [ ] `SystemStatusPage` gated by `core.health.view` (owner)
- [ ] Sentry test event carries company_id + user_id tags
- [ ] Failing check (stopped Redis in test harness via fake) renders red on status page

---

## Build Manifest

```
app/Providers/AppServiceProvider.php (Health::checks)
config/{health,pulse,sentry}.php
app/Filament/App/Pages/SystemStatusPage.php
resources/views/filament/app/pages/system-status.blade.php
tests/Feature/Core/HealthEndpointTest.php
```

---

## Related

- [[architecture/deployment]] — health checks in deploy + monitoring table
- [[architecture/queue-jobs]]
- [[architecture/packages]] (`spatie/laravel-health`, `sentry/sentry-laravel`)
