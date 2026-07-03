---
domain: core
module: health-monitoring
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Monitoring *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SystemStatusPage` (/app) | #6 Dashboard custom page *(assumed — status tiles + poll; closest blueprint)* | [[../../../architecture/patterns/page-blueprints#Dashboard]] | green/red per check + last-checked timestamp; polling 60s; owner-only ([[./features/system-status-page]]) |
| Pulse dashboard (/admin `/pulse`) | external package dashboard (Laravel Pulse's own UI — not a FlowFlex-built Filament artifact) | n/a — linked from `/admin` Monitoring nav | admin-guard only ([[./features/pulse-dashboard]]) |
| Horizon dashboard (/admin `/horizon`) | external package dashboard (Laravel Horizon's own UI — not a FlowFlex-built Filament artifact) | n/a — linked from `/admin` Monitoring nav | admin-guard only |

**Access contract (mandatory):** `SystemStatusPage` is a custom page and MUST state its gate explicitly — Filament does not auto-gate custom pages:
`canAccess() = Auth::user()->can('core.health.view-any') && BillingService::hasModule('core.health')`
per [[../../../architecture/filament-patterns]] #1 — **owner-only**. `/pulse` and `/horizon` are external package dashboards gated on the admin guard only (FlowFlex staff), never exposed in a company panel. `GET /health` is a machine endpoint (not a Filament artifact) — throttled + token-guarded for detail per [[security]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| — (configuration-only module) | n/a | Owns no tables and performs no writes — health results are ephemeral (per-request `spatie/laravel-health`); Pulse/Horizon state is Redis-backed and owned by their packages; no write path or concurrent-edit surface |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
