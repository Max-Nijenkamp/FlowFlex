---
domain: analytics
module: report-builder
feature: report-runner
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report Runner

Executes a report definition into rows - CompanyScope-safe, Eloquent-only - for preview and export.

## Behaviour

- `ReportRunner::run(Report, ?limit)` composes an Eloquent query over the whitelisted source under `CompanyScope`.
- Applies filters (operator-validated), grouping + SQL aggregations (count/sum/avg/min/max), sorting.
- Preview caps at 100 rows; export runs unlimited but **chunked** via `ExportReportJob` (`exports` queue).
- Never raw SQL; never non-whitelisted columns; never another company's rows.

## UI

- **Kind**: background (+ export action). The runner has no page; its preview output renders inside [[report-composer]], and export is a queued action on [[saved-reports]].
- **Page**: none (invoked by composer preview + saved-report run/export).
- **Layout**: n/a directly; preview table lives in the composer.
- **Key interactions**: preview -> synchronous capped query; export -> `ExportReportJob` queued -> notification + download link.
- **States**: (export action) idle - loading ("preparing...") - error (generation failed -> retry) - done (download link).
- **Gating**: run requires `analytics.reports.run`; export requires `analytics.reports.export`; both throttled.

## Data

- Owns / writes: nothing persistent (transient export file under `companies/{id}/exports/`).
- Reads: the whitelisted source model's columns under `CompanyScope`.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: definitions from [[saved-reports]] / [[report-composer]]; source models via [[source-registry]].
- Feeds: rows to the composer preview; files to a download / to [[../../scheduled-exports/_module|analytics.exports]] when scheduled.
- Shared entity: source models (owned by source domains, read-only).

## Unknowns

- Result caching - none in v1 *(assumed)*. See [[../unknowns]].
- Export formats (Excel/CSV/PDF) - *(assumed Excel+CSV)*.

## Related

- [[../_module|Report Builder]] - [[report-composer]] - [[saved-reports]] - [[../../../../architecture/queue-jobs]]
