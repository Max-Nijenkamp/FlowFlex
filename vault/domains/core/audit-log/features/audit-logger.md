---
domain: core
module: audit-log
feature: audit-logger
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Audit Logger (write path + retention)

Parent: [[../_module]] · See [[../architecture]] · [[../data-model]]

The single entry point through which every domain records audit activity, plus the daily retention prune. No domain writes `activity_log` directly.

## Behaviour

- `AuditLogger::log(string $event, Model $subject, ?User $causer, array $properties = []): void` wraps spatie `activity()`, **force-sets `company_id` from `CompanyContext`** (never client-supplied), and strips PII keys via the denylist ([[pii-denylist]]) before persisting.
- `$causer` is null for system/job-driven writes.
- State-machine transitions auto-log a `state-transition` row (from/to) via the transition base class ([[../../../architecture/patterns/states]]) — no per-domain wiring.
- `PruneAuditLogCommand` (daily 04:30) deletes rows older than the per-company retention cutoff (default 2 years) — naturally idempotent. Retention config comes from company privacy settings ([[../../../architecture/data-lifecycle]]).

## UI

- **Kind**: background
- **Page**: background (no page) — the logger is a service called in-process; the prune is a scheduled console command. Written rows surface in [[log-browser]].
- **Layout**: n/a. Triggers: (1) any domain write / state transition calls `AuditLogger::log`; (2) the scheduler runs `PruneAuditLogCommand` daily at 04:30.
- **Key interactions**: none directly — invoked programmatically. Operators may observe pruning via logs/Horizon.
- **States**: empty = no activity to log / nothing past retention to prune · loading = prune command running · error = write/prune failure logged (does not block the originating domain write beyond its own transaction rules) · selected = n/a.
- **Gating**: no user gate on the write path (inherits caller context); the prune runs under system context with forced `company_id` per row.

## Data

- Owns / writes: `activity_log` (this module's only table) — the logger inserts rows; the prune deletes expired rows.
- Reads: `CompanyContext` for `company_id`; per-company retention setting (read-only, from company settings / [[../../../architecture/data-lifecycle]]); the PII denylist definition.
- Cross-domain writes: none — the logger writes only `activity_log` on behalf of every domain; it never touches another domain's tables ([[../../../../security/data-ownership]]). Other domains reach it by **calling the service**, not by writing the log table themselves.

## Relations

- Consumes: none as domain events — it is invoked in-process by every domain's write operations and by the state-transition base class.
- Feeds: none (fires no events).
- Shared entity: `activity_log` subject/causer are polymorphic references to models owned by other domains/platform; stored read-only as type + id.

## Test Checklist

### Unit
- [ ] `company_id` force-set from `CompanyContext` even when a different `company_id` is passed in `properties`
- [ ] PII keys stripped from `properties` before persist (denylist strip returns field names only)

### Feature (Pest)
- [ ] `AuditLogger::log` inserts one row with `company_id` from context; company A's row invisible to company B (tenant isolation)
- [ ] A state-machine transition writes a `state-transition` row carrying from/to via the transition base class
- [ ] `PruneAuditLogCommand` deletes rows older than the per-company retention cutoff and leaves newer rows (idempotent on re-run)

## Related

- [[../_module|Audit Log]] · [[log-browser]] · [[pii-denylist]] · [[../../../architecture/patterns/states]] · [[../../../architecture/data-lifecycle]]
