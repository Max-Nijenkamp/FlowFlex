---
domain: analytics
module: scheduled-exports
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Scheduled Exports — Decisions

---

## ADR: Transactional next_run_at advance = idempotency guard

The single most important correctness control: `RunScheduledExportsCommand` advances `next_run_at` in the **same transaction** as writing the `bi_export_log` row. A re-run (crash, overlapping scheduler tick) therefore never double-sends. Per-schedule try/catch isolates failures so one bad schedule can't block the batch.

---

## ADR: Read sources via their own read paths; deliver via shared mail

An export reads its source (report/dashboard/financial) through that domain's read path (`ReportRunner`, dashboard snapshot, statement read) and delivers via `foundation.email`. Analytics writes only `bi_export_schedules` + `bi_export_log` — never another domain's tables, never a bespoke mail transport ([[../../../security/data-ownership]]).

---

## ADR: Large files via signed link, files on the company disk

Files are stored at `companies/{id}/exports/` on the company disk. Attachments over 10 MB *(assumed)* are replaced by a tenant-scoped, time-limited signed link — bounds email size and keeps files access-controlled.

---

## Implementation Notes

- Recipients are company users only *(assumed: no external emails v1)*.
- Send times computed in the company timezone; `PruneExportLogCommand` trims the log after 90 days *(assumed)*.
- Every 15 min tick keeps the run loop simple (frequency resolved against `next_run_at`, not cron per schedule).
