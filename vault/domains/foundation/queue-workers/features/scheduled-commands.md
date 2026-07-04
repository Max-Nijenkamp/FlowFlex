---
domain: foundation
module: queue-workers
feature: scheduled-commands
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Scheduled Commands (`scheduler` service)

A dedicated `scheduler` container (`php artisan schedule:work`) enqueues recurring work; the Horizon workers process it.

## Behaviour

- `scheduler` runs as a **separate Docker service** ([[../../docker-environment/_module|docker]]) — not a cron on the host.
- Registrations live in `routes/console.php`.
- Every scheduled command: `withoutOverlapping()` (no double-run) + `onOneServer()` (single instance in a multi-node deploy).
- Recurring tenant work is dispatched as jobs carrying `company_id` → processed under `WithCompanyContext`.

## UI

- **Kind**: background (infrastructure). Visible only via Horizon (admin) and the scheduler's queue heartbeat.

## Data

- Owns: no tables (uses Laravel-standard `jobs`, `job_batches`). Cross-domain writes: none.

## Relations

- Consumes: nothing. Feeds: any domain that registers a scheduled command (reports, digests, cleanups).
- Shared entity: `routes/console.php` schedule registry.

## Test Checklist

### Unit
- [x] A scheduled command declares `withoutOverlapping()` + `onOneServer()`

### Feature (Pest)
- [x] An overlapping run is skipped (single instance); recurring tenant work dispatches carrying `company_id`

## Unknowns

> [!warning] UNVERIFIED — the concrete schedule registrations (none domain-specific remain after the strip);
> per-command frequency. See [[../unknowns]].

## Related

- [[../_module|Queue Workers]] · [[job-processing]] · [[../../../../architecture/queue-jobs]]
