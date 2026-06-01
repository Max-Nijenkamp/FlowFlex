---
type: module
domain: Foundation
panel: (scaffold)
module-key: foundation.queues
status: planned
color: "#4ADE80"
---

# Queue Workers & Scheduler

Laravel Horizon setup, queue worker configuration, the `WithCompanyContext` job middleware, and the scheduled command runner. Background processing infrastructure all domains depend on.

## Core Features

- Laravel Horizon installed and configured — dashboard at `/horizon` (admin guard only)
- Queue connection: Redis (`DB 3`)
- Named queues per concern: `default`, `domain-events`, `notifications`, `hr`, `finance`, `webhooks`, `exports`, `imports`
- Horizon supervisors with per-queue worker counts and priorities
- `WithCompanyContext` job middleware — restores tenant context in workers (see [[architecture/multi-tenancy]])
- Failed job handling: `failed_jobs` table, 30-day retention, Horizon failure alerts
- Scheduler: `app/Console/Kernel.php` recurring commands (overdue invoices, recurring invoices, leave balance recalc, soft-delete purge, failed-job prune)
- Horizon runs as a separate Docker service (see [[domains/foundation/docker-environment]])
- Reverb WebSocket worker runs as a separate Docker service

## Data Model

Laravel queue tables — no custom tables:

| Table | Purpose |
|---|---|
| `jobs` | Pending queued jobs |
| `failed_jobs` | Failed jobs (30-day retention) |
| `job_batches` | Batched job tracking |

## Filament

No Filament resources — infrastructure. Horizon provides its own dashboard at `/horizon`.

## Cross-Domain

- Every domain dispatches jobs and queued listeners onto these queues
- `WithCompanyContext` is mandatory on any job touching tenant models

## Related

- [[architecture/queue-jobs]] — full Horizon config, queue names, retry strategy
- [[architecture/multi-tenancy]] — WithCompanyContext middleware
- [[domains/foundation/docker-environment]] — Horizon + Reverb services
- [[domains/foundation/email-setup]] — emails dispatch on the `notifications` queue
