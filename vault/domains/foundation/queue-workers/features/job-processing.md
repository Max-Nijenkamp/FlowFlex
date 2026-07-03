---
domain: foundation
module: queue-workers
feature: job-processing
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Job Processing (Horizon + prioritised Redis queues)

Horizon processes Redis-backed queues in priority order; the dashboard is an admin-only observability surface.

## Behaviour

- `defaults` supervisor processes queues in order: `domain-events, notifications, hr, finance, webhooks, exports, imports, default`.
- `hr`/`finance` are **declared but empty** until those domains return (kept to avoid supervisor churn).
- Failed jobs land in `failed_jobs` (30-day retention); retryable via Horizon.
- `/horizon` dashboard gated to the `admin` guard.
- Tenant jobs restore context via `WithCompanyContext` ([[../../multi-tenancy-layer/features/queue-context]]).

## UI

- **Kind**: custom-page (external — Laravel Horizon's own dashboard at `/horizon`, not a Filament page).
- **Page**: Horizon dashboard (`/horizon`) — throughput, failed jobs, metrics.
- **Gating**: `admin` guard only (`HorizonServiceProvider` gate); tenant users blocked.
- **States**: healthy (green supervisors) · failing (failed-job list) · paused.

## Data

- Owns: `jobs`, `failed_jobs`, `job_batches` (Laravel-standard). Cross-domain writes: none.

## Relations

- Consumes: jobs dispatched by every domain. Feeds: async side-effects (mail, exports, event listeners).
- Shared entity: the queue-name registry in `config/horizon.php`.

## Test Checklist

### Unit
- [ ] The `defaults` supervisor declares the queue priority order

### Feature (Pest)
- [ ] `/horizon` reachable by the `admin` guard, blocked for tenant users
- [ ] A failed job lands in `failed_jobs` and is retryable

## Unknowns

> [!warning] UNVERIFIED — push alerting on failures is not present (dashboard-only) — flagged as an
> opportunity ([[../../_opportunities]]). Gate role list UNVERIFIED — see [[../unknowns]].

## Related

- [[../_module|Queue Workers]] · [[scheduled-commands]] · [[../../../../infrastructure/queue-horizon]]
