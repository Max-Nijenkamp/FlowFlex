---
domain: analytics
module: data-views
feature: view-explorer
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# View Explorer

The gallery + render surface: pick an available cross-domain view, set a date range, and read its aggregated chart + table.

## Behaviour

- Gallery lists views from `DataViewRegistry::available()` (module-filtered).
- Opening a view calls `run(DateRange)` → `DataViewResult` (columns + rows), rendered as chart + table.
- A date-range control re-resolves the view; results cached per `(view, range)`.
- Rows expose drill targets handed to [[drill-down]].

## UI

- **Kind**: custom-page — gallery + report render ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `DataViewsPage` at `/analytics/data-views` *(route assumed)* — gallery; selecting a view renders it in-page.
- **Layout**: top = view gallery cards (module-filtered); on select → header (view name, date-range picker, export) + apex chart + results table with drill affordance.
- **Key interactions**: click a view card → resolve + render; change date range → recompute (skeleton while loading); click an aggregate row → drill-down ([[drill-down]]); export button → queued Excel.
- **States**: empty (no active source modules → "no cross-domain views available; activate the source modules" ) · loading (skeleton chart + table) · error (resolution fails → toast + retry) · selected (view card highlighted, its render shown).
- **Gating**: visible with `analytics.data-views.view-any`; export requires `analytics.data-views.export`.

## Data

- Owns / writes: nothing.
- Reads: each source domain's read path inside `DataView::run()` under `CompanyContext` (CRM, Finance, HR, Projects, Marketing).
- Cross-domain writes: none — Analytics owns no tables here ([[../../../../security/data-ownership]]).

## Relations

- Consumes: available views from [[view-registry]]; source data from CRM/Finance/HR/Projects/Marketing read paths.
- Feeds: aggregate rows to [[drill-down]]; result sets to [[view-export]].
- Shared entity: none persisted.

## Test Checklist

### Unit
- [ ] `DataViewResult` shapes columns/rows + drill targets per view contract

### Feature (Pest)
- [ ] `run(DateRange)` resolves via source domains' read paths under `CompanyContext` — company A never sees B aggregates
- [ ] Result cached per `(view, range)`; range change recomputes
- [ ] Gallery lists only views whose `requiredModules()` are all active

### Livewire
- [ ] `DataViewsPage` renders gallery + selected view chart/table; no-source-modules empty state shows activation hint
- [ ] Denied without `analytics.data-views.view-any`

## Unknowns

- Whether gallery + render are one page or two — *(assumed one)*. See [[../unknowns]].
- Chart type per view — *(assumed)* apex, per view.

## Related

- [[../_module|Cross-Domain Data Views]] · [[view-registry]] · [[drill-down]] · [[view-export]]
