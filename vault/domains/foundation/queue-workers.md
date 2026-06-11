---
type: module
domain: Foundation
domain-key: foundation
panel: (scaffold)
module-key: foundation.queues
status: complete
priority: v1-core
depends-on: [foundation.scaffold, foundation.tenancy]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [queues]
tables: []
permission-prefix: ""
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Queue Workers & Scheduler

Laravel Horizon setup, queue worker configuration, the `WithCompanyContext` job middleware wiring, and the scheduled command runner. Background processing infrastructure all domains depend on.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/laravel-scaffold\|foundation.scaffold]] | Horizon/Redis installed + configured |
| Hard | [[domains/foundation/multi-tenancy-layer\|foundation.tenancy]] | `WithCompanyContext` middleware exists |

---

## Core Features

- Laravel Horizon installed and configured — dashboard at `/horizon` (admin guard only)
- Queue connection: Redis (`DB 3`)
- Named queues per concern: `default`, `domain-events`, `notifications`, `hr`, `finance`, `webhooks`, `exports`, `imports`
- Horizon supervisors with per-queue worker counts and priorities (config: [[architecture/queue-jobs]])
- `WithCompanyContext` job middleware mandatory on any job touching tenant models
- Failed job handling: `failed_jobs` table, 30-day retention, Horizon failure alerts
- Scheduler: recurring commands registered as modules ship them (overdue invoices, leave accrual, soft-delete purge, failed-job prune) — every scheduled command `withoutOverlapping()` + `onOneServer()` per the idempotency rules in [[architecture/queue-jobs]]
- Horizon + Reverb run as separate Docker services ([[domains/foundation/docker-environment]])

---

## Data Model

Laravel queue tables — no custom tables:

| Table | Purpose |
|---|---|
| `jobs` | Pending queued jobs |
| `failed_jobs` | Failed jobs (30-day retention) |
| `job_batches` | Batched job tracking |

## DTOs / Filament / Permissions

None — infrastructure. Horizon provides its own dashboard at `/horizon`, gated to the `admin` guard.

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `queue:prune-failed --hours=720` | — | daily | framework-provided |
| `PruneSoftDeletedRecordsCommand` *(arrives with core modules)* | default | daily 04:00 | WHERE `deleted_at < now()-12mo` guard |

---

## Test Checklist

- [ ] Job dispatched to a named queue lands on that queue (Horizon config honored)
- [ ] Queued listener with `WithCompanyContext` restores the right company context
- [ ] Failed job (forced exception, 3 tries) appears in `failed_jobs`
- [ ] `schedule:list` shows registered commands with `withoutOverlapping`
- [ ] Horizon dashboard inaccessible to tenant users (admin guard only)

---

## Build Manifest

```
config/horizon.php (supervisors, queue priorities per architecture/queue-jobs)
config/queue.php (redis connection, DB 3)
routes/console.php (scheduler registrations)
app/Providers/HorizonServiceProvider.php (admin-guard gate)
tests/Feature/Foundation/QueueContextTest.php
```

---

## Related

- [[architecture/queue-jobs]] — full Horizon config, queue names, retry strategy, idempotency rules
- [[architecture/multi-tenancy]] — WithCompanyContext middleware
- [[domains/foundation/docker-environment]] — Horizon + Reverb services
- [[domains/foundation/email-setup]] — emails dispatch on the `notifications` queue
