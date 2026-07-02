---
domain: analytics
module: report-builder
feature: report-composer
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Report Composer

The no-code builder canvas: pick a source, choose columns, add filters + grouping + sorting, and see a live preview.

## Behaviour

- Choose a data source from `ReportSourceRegistry::available()` (module-filtered).
- Pick columns from the source's whitelist; add filters (field + operator + value, AND/OR), grouping, aggregations, sorting.
- Live preview (first 100 rows) via `ReportRunner::run($report, 100)`.
- Save the definition to `bi_reports` (registry-validated on write).

## UI

- **Kind**: custom-page (report builder) — multi-pane picker + preview ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `ReportBuilderPage` at `/analytics/reports/build` *(route assumed)*.
- **Layout**: left rail = source + column picker (whitelisted only); centre = filter/grouping/sorting builder; bottom/right = live preview table; top = name + save + export.
- **Key interactions**: select source -> column list populates; toggle columns; add filter rows (field/operator/value, AND-OR); set grouping + aggregation; change any input -> preview re-runs (debounced, skeleton while loading); save -> persist definition.
- **States**: empty (no source chosen -> "pick a data source") - loading (preview skeleton) - error (invalid column/operator -> inline validation; unavailable source -> notice) - selected (source highlighted, its columns shown).
- **Gating**: visible with `analytics.reports.view-any`; save requires `analytics.reports.create`; preview requires `analytics.reports.run`.

## Data

- Owns / writes: `bi_reports` (definition on save).
- Reads: `ReportSourceRegistry` for source/column vocabulary; source model's whitelisted columns via `ReportRunner` for the preview (CompanyScope-safe).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: sources from [[source-registry]].
- Feeds: definitions to [[saved-reports]]; run requests to [[report-runner]].
- Shared entity: source keys (owned by source domains, referenced).

## Unknowns

- Definition storage shape + operator set - see [[../unknowns]].
- Whether build + view are one page or two - *(assumed one builder + a resource for saved)*.

## Related

- [[../_module|Report Builder]] - [[source-registry]] - [[report-runner]] - [[saved-reports]]
