---
domain: analytics
module: report-builder
feature: report-runner
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
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

## Test Checklist

### Unit
- [ ] Query composition uses Eloquent only (no raw SQL) and always applies `CompanyScope`.
- [ ] Aggregations count/sum/avg/min/max compute correctly per grouped column over fixtures.
- [ ] A non-whitelisted column in the definition is refused at run time, not just at save.

### Feature (Pest)
- [ ] `run($report, 100)` returns only the current company's rows — cross-company rows never appear (the isolation guarantee).
- [ ] `run($report, null)` via `ExportReportJob` chunks large result sets and writes the file under `companies/{id}/exports/`.
- [ ] `ExportReportJob` runs under `WithCompanyContext`; a report on a now-inactive source cannot run.
- [ ] Export throttled by the `exports` limiter (5/hr per company).

## Unknowns

- Result caching - none in v1 *(assumed)*. See [[../unknowns]].
- Export formats (Excel/CSV/PDF) - *(assumed Excel+CSV)*.

## Related

- [[../_module|Report Builder]] - [[report-composer]] - [[saved-reports]] - [[../../../../architecture/queue-jobs]]
