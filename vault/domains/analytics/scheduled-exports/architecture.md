---
domain: analytics
module: scheduled-exports
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Scheduled Exports — Architecture

## The run loop

`RunScheduledExportsCommand` runs every 15 min:

1. Select due schedules: `next_run_at <= now()`, computed in the **company timezone**, `is_active = true`.
2. Per schedule, in a try/catch: generate the artifact via the **source's own read path** (report → `ReportRunner`; dashboard → PDF snapshot; financial → statement read), then mail it.
3. **Advance `next_run_at` in the same transaction** as writing the log row — the idempotency guard against double-sends.
4. Write a `bi_export_log` row (success/failed, file path or error).

Failures are isolated per schedule (one bad schedule never blocks the batch) and the schedule continues next cycle.

---

## Services & Actions

- `RunScheduledExportsCommand` — the due-schedule run loop above (`exports` queue).
- `PruneExportLogCommand` — daily; prunes log rows older than 90 days *(assumed)*.
- `ExportScheduleService::pause() / resume()` — toggle `is_active`.
- `ScheduledExportMail` — `FlowFlexMailable`, `ShouldQueue`; attaches the file or a signed link for large files (> 10 MB *(assumed)*).

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunScheduledExportsCommand` | exports | every 15 min | `next_run_at` advanced transactionally — no double sends |
| `PruneExportLogCommand` | default | daily | date guard |

---

## Events

None fired, none consumed. Generation is schedule-driven, not event-driven.

---

## Filament Artifacts

**Nav group:** Reports

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ScheduledExportResource` | #1 CRUD resource | tweaks: custom-header-actions (pause/resume), relation-manager-timeline (delivery log, read-only) | manage schedules; next-run column |

**Access contract (mandatory):** `canAccess() = Auth::user()->can('analytics.exports.view-any') && BillingService::hasModule('analytics.exports')` per [[../../../architecture/filament-patterns]] #1.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Schedule CRUD (form) | Optimistic | `updated_at` stale-check → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Run dispatch (`next_run_at` advance) | Pessimistic | Cursor advanced in the same transaction as the `bi_export_log` write ([[./decisions]]) — no double sends |
| Delivery log | n/a | Append-only, written by the run command |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Caching

None — the run loop reads live sources at generation time.

---

## Search & Realtime

- Search: none.
- Realtime: none — schedules run on the scheduler; the log updates on completion.

---

## Security Notes

See [[./security]]. Generated files are stored under the **company disk** at `companies/{id}/exports/`; large files are delivered via a **tenant-scoped, time-limited signed link** rather than attachment. Recipients are company users only ([[../../../architecture/security]]).
