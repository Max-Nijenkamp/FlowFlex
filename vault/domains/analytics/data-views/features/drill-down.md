---
domain: analytics
module: data-views
feature: drill-down
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Drill-Down

Click an aggregate cell to expand it into the underlying records — the "why is this number what it is" path.

## Behaviour

- Each `DataViewResult` row carries a drill key; clicking it calls `DataView::drillDown($key, $range)`.
- Drill-down reads the same source domains' read paths under `CompanyContext`, returning the constituent records.
- Underlying rows must reconcile with the aggregate (a tested invariant).
- One level in v1 *(assumed)* — aggregate → records, not records → deeper.

## UI

- **Kind**: custom-page (slide-over/expansion within [[view-explorer]]) — not a standalone page.
- **Page**: rendered inside `DataViewsPage`; drill result opens in a slide-over or expanded panel.
- **Layout**: slide-over listing the underlying records (table) with a back-to-aggregate control.
- **Key interactions**: click aggregate row → slide-over opens with `drillDown()` records (skeleton while loading); close → return to the view.
- **States**: empty (aggregate had zero underlying rows → "no records") · loading (skeleton rows) · error (toast + retry) · selected (source row highlighted, slide-over open).
- **Gating**: `analytics.data-views.view-any` (same as the parent view).

## Data

- Owns / writes: nothing.
- Reads: source domains' record-level read paths under `CompanyContext`.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: drill keys from [[view-explorer]]; record reads from the source domains.
- Feeds: nothing downstream (terminal detail view).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Drill key round-trips: aggregate row → `drillDown($key, $range)` scope

### Feature (Pest)
- [ ] Drill records reconcile with the aggregate value (sum/count invariant)
- [ ] Drill reads run under `CompanyContext` — no cross-company records

### Livewire
- [ ] Slide-over opens with records on aggregate click; zero-row aggregate shows "no records"
- [ ] Denied without `analytics.data-views.view-any`

## Unknowns

- Single vs multi-level drill — *(assumed single)*. See [[../unknowns]].
- Whether drilled records are exportable — *(assumed no; only the aggregate exports)*.

## Related

- [[../_module|Cross-Domain Data Views]] · [[view-explorer]] · [[view-registry]]
