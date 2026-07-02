---
domain: core
module: health-monitoring
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Health Monitoring

System health checks, a Laravel Pulse metrics dashboard, and Horizon queue monitoring — surfaced to company owners (simplified `SystemStatusPage` in `/app`) and to FlowFlex staff (full Pulse/Horizon dashboards in `/admin`). A configuration module: no tables, no DTOs, no services of its own.

- **module-key:** `core.health` · **panel:** app + admin · **priority:** v1
- **fires-events:** none · **consumes-events:** none

## Sibling notes

- [[architecture]] — `Health::checks` list, Pulse / Horizon / Sentry wiring
- [[security]] — `/health` rate limiter, admin-guard on Pulse/Horizon, minimal anon output
- Features: [[features/health-endpoint]] · [[features/pulse-dashboard]] · [[features/system-status-page]]

No `data-model.md` — all health data is ephemeral (Redis-backed Pulse, Horizon). No `api.md` — no events or DTOs.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.queues | Horizon checks + dashboard |
| Hard | foundation.panels | status page in `/app`, full dashboards via `/admin` |

## Core Features

- `GET /health` — JSON health check via `spatie/laravel-health`: database, Redis, Meilisearch, Horizon running, queue depth (`domain-events`, `notifications`), disk space (warn >70%), environment check
- Laravel Pulse dashboard at `/pulse` — admin-only: slow queries (>100ms), exception rate, queue throughput/depth, cache hit/miss, server resources
- Laravel Horizon dashboard at `/horizon` — admin guard only: queue depth, throughput, failed jobs with stack trace, worker count
- Sentry integration: production exceptions captured with `company_id` and `user_id` tags
- `SystemStatusPage` (`/app`, owner): green/red per check, last-checked timestamp, polling 60s

## Test Checklist

- [ ] `GET /health` returns JSON with all registered checks
- [ ] `/horizon` + `/pulse` inaccessible to tenant non-owner users
- [ ] `SystemStatusPage` gated by `core.health.view` (owner)
- [ ] Sentry test event carries `company_id` + `user_id` tags
- [ ] Failing check (stopped Redis via fake) renders red on the status page

## Build Manifest (corrected to flat paths)

```
app/Providers/AppServiceProvider.php (Health::checks)
config/{health,pulse,sentry}.php
app/Filament/App/Pages/SystemStatusPage.php
resources/views/filament/app/pages/system-status.blade.php
tests/Feature/Core/HealthEndpointTest.php
```

Spec paths were already flat (no `Core/` subdir) — no correction needed.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none | — | Health monitoring fires no domain events |
| consumes | none | — | Consumes no domain events; reads infrastructure liveness + telemetry directly |
| reads | `BillingService::hasModule('core.health')` | [[../billing-engine/_module]] | `SystemStatusPage` gate — page shown only if the module is active |

Data ownership: health-monitoring owns **no tables of its own** — it is configuration-only. All health data is ephemeral (Redis-backed Pulse/Horizon, per-request `spatie/laravel-health` results, Sentry telemetry). It reads infrastructure liveness and (read-only) `BillingService::hasModule` for one page gate, and effects other domains only via events (there are none) ([[../../../security/data-ownership]]).

## Related

- [[../billing-engine/_module]] — `hasModule` gate on `SystemStatusPage`
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../infrastructure/deployment]] — health checks in deploy + monitoring
- [[../../../architecture/queue-jobs]] · [[../../../security/data-ownership]]
- [[../../../architecture/packages]] (`spatie/laravel-health`, `sentry/sentry-laravel`) · [[../../../glossary]]
