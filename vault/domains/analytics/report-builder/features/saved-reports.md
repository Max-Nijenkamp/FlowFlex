---
domain: analytics
module: report-builder
feature: saved-reports
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Saved Reports

The library of saved report definitions: list, re-run, export, and schedule.

## Behaviour

- Saved `bi_reports` definitions are listed and can be re-run (identical output for the same data).
- Row actions: run (preview), export (queued), edit (→ composer), delete (soft).
- A saved report is a schedulable source for [[../../scheduled-exports/_module|analytics.exports]].

## UI

- **Kind**: simple-resource — CRUD list of saved reports ([[../../../../architecture/patterns/filament-resource-checklist]]).
- **Page**: `ReportResource` at `/analytics/reports`.
- **Columns**: name, data source (label), owner, last run *(assumed)*, updated.
- **Form fields**: opens the [[report-composer]] page for edit (definition is complex).
- **Filters**: source, owner.
- **Row actions**: run, export, edit, delete, "schedule" (→ create an export schedule).
- **States**: empty ("build your first report" CTA) · loading (table skeleton) · error (run on inactive source → notice) · selected (row → run/edit).
- **Gating**: view with `analytics.reports.view-any`; edit/delete with `analytics.reports.create`; run/export with the respective permissions.

## Data

- Owns / writes: `bi_reports` (this module's table).
- Reads: source models via [[report-runner]] on run/export (CompanyScope-safe).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: definitions from [[report-composer]]; execution from [[report-runner]].
- Feeds: a saved report reference to [[../../scheduled-exports/_module|analytics.exports]] (as a `source_type: report`).
- Shared entity: source keys (read-only).

## Test Checklist

### Unit
- [ ] A saved report re-run with unchanged data produces identical output (deterministic ordering).

### Feature (Pest)
- [ ] List shows only the current company's saved reports (tenant isolation).
- [ ] Run on a now-inactive source surfaces the "source unavailable" notice, no query executes.
- [ ] "Schedule" row action hands a `source_type: report` reference to `analytics.exports` (no cross-domain write).
- [ ] Soft-delete hides the report from the list and from schedulable sources.

### Livewire
- [ ] `canAccess()` false without `analytics.reports.view-any` or when module inactive.
- [ ] Row actions gate per verb: edit/delete on `create`, run on `run`, export on `export`.
- [ ] Export row action is throttled by the `exports` limiter.

## Unknowns

- `last run` tracking + report cloning/templating — see [[../unknowns]].

## Related

- [[../_module|Report Builder]] · [[report-composer]] · [[report-runner]] · [[../../scheduled-exports/_module|analytics.exports]]
