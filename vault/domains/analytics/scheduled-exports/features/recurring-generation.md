---
domain: analytics
module: scheduled-exports
feature: recurring-generation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Recurring Generation

The scheduled run loop that generates each due export and emails it — exactly once per cycle.

## Behaviour

- `RunScheduledExportsCommand` runs every 15 min: selects due schedules (`next_run_at <= now`, company-TZ, active).
- Per schedule (try/catch): generate the artifact via the source's read path (report → `ReportRunner`; dashboard → PDF snapshot; financial → statement read), store under `companies/{id}/exports/`, mail it (attachment or signed link for large files).
- Advance `next_run_at` in the **same transaction** as the `bi_export_log` write — no double sends.
- A failure is caught + logged; the schedule continues next cycle.

## UI

- **Kind**: background — a scheduled console command, no page. Its outcomes surface in [[delivery-log]] and the recipient's inbox.
- **Page**: none.
- **Layout**: n/a.
- **Key interactions**: n/a (scheduler-driven).
- **States**: n/a (per-run outcome recorded as success/failed in the log).
- **Gating**: runs system-side under each company's `CompanyContext`; no user permission.

## Data

- Owns / writes: `bi_export_log` (one row per run), advances `bi_export_schedules.next_run_at`; writes the file to the company disk.
- Reads: the source via its domain's read path (`ReportRunner` / dashboard snapshot / statement).
- Cross-domain writes: none — delivery is handed to `foundation.email` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: due schedules from [[schedule-management]]; source data from [[../../report-builder/_module|analytics.reports]] / [[../../dashboards/_module|analytics.dashboards]] / [[../../finance/financial-reporting/_module|finance.reporting]].
- Feeds: log rows to [[delivery-log]]; email via [[../../foundation/email-setup/_module|foundation.email]] on [[../../foundation/queue-workers/_module|foundation.queues]].
- Shared entity: recipient users (by id).

## Unknowns

- Run cadence precision + failure-notification to owner — see [[../unknowns]].

## Related

- [[../_module|Scheduled Exports]] · [[schedule-management]] · [[delivery-log]] · [[../../../../architecture/queue-jobs]]
