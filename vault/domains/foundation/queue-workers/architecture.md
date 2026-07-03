---
domain: foundation
module: queue-workers
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Queue Workers & Scheduler — Architecture

Horizon, Redis queues, the `WithCompanyContext` job-middleware wiring, and the scheduled-command runner. Background processing every domain depends on. Queue priority order, topology diagram, and tables live in [[_module]]; authoritative Horizon config in [[../../../infrastructure/queue-horizon]].

## Filament Artifacts

**Filament Artifacts:** None (backend module — background processing. Its only dashboard is Laravel Horizon's own `/horizon` UI, gated to the `admin` guard via `HorizonServiceProvider` — an external Laravel surface, not a Filament panel artifact).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Scheduled command execution | Pessimistic (atomic lock) | `withoutOverlapping()` + `onOneServer()` — a Redis cache atomic-lock guaranteeing a single instance across workers/nodes ([[../../../architecture/queue-jobs]]) |
| Job / listener record writes | n/a | Each listener writes only its own domain's tables under `WithCompanyContext`; queue-workers owns no tenant records (only Laravel-standard `jobs`/`failed_jobs`/`job_batches`) — those write paths carry their own domain's tier |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
