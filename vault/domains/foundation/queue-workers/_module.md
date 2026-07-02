---
domain: foundation
module: queue-workers
type: module
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Queue Workers & Scheduler

`foundation.queues` — Horizon, Redis queues, the `WithCompanyContext` job middleware wiring, and the scheduled-command runner. Background processing every domain depends on. Authoritative infra config: [[../../../infrastructure/queue-horizon]].

## Queues (verified in `config/horizon.php`)

`defaults` supervisor processes, in priority order:

```
domain-events, notifications, hr, finance, webhooks, exports, imports, default
```

> [!note] `hr` and `finance` are empty queues
> These queue names predate the HR/Finance/CRM strip. The queues are still declared in Horizon config but **no jobs are dispatched to them until those domains are rebuilt**. Left in place so the strip didn't churn the supervisor config.

## Topology

```mermaid
flowchart LR
    App[app · web] -->|dispatch| RQ[(Redis queues)]
    Horizon[horizon service] -->|process| RQ
    Scheduler[scheduler service\nschedule:work] -->|enqueue recurring| RQ
    RQ --> W[WithCompanyContext\nrestores tenant]
    W --> Fail[(failed_jobs)]
```

- `horizon` and `scheduler` run as **separate Docker services** ([[../docker-environment/_module|docker]]); scheduler = `php artisan schedule:work`.
- `/horizon` dashboard gated to the `admin` guard.
- `WithCompanyContext` mandatory on any job touching tenant models.
- Every scheduled command: `withoutOverlapping()` + `onOneServer()` ([[../../../architecture/queue-jobs]]).

## Queue Tables

`jobs`, `failed_jobs` (30-day retention), `job_batches` — Laravel-standard, no custom tables.

## Test Checklist (verified)

- [x] Queued listener with `WithCompanyContext` restores the right company (`tests/Feature/QueueContextTest.php`)
- [ ] Horizon dashboard inaccessible to tenant users (admin guard only)

No DTOs / Filament / Permissions — infrastructure.

## Build Manifest

```
config/horizon.php (supervisors, queue priorities)
config/queue.php (redis connection)
routes/console.php (scheduler registrations)
app/Providers/HorizonServiceProvider.php (admin-guard gate)
tests/Feature/QueueContextTest.php
```

## Related

- [[../../../infrastructure/queue-horizon]] — full Horizon config
- [[../../../architecture/queue-jobs]]
- [[../multi-tenancy-layer/_module|Multi-Tenancy Layer]] — WithCompanyContext
- [[../email-setup/_module|Email Setup]] — mail on the `notifications` queue
