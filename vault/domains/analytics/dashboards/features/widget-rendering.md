---
domain: analytics
module: dashboards
feature: widget-rendering
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Widget Rendering

Resolves each placed widget's metric into cached data and draws it — stat card, chart, table, or gauge.

## Behaviour

- `WidgetDataService::resolve(Widget, DateRange)` resolves the widget's `metric_key` against `MetricRegistry`, applies filters + range, returns cached data (`company:{id}:bi:widget:{widget}:{range}`, TTL 15 min *(assumed)*).
- A widget whose metric is unregistered or whose module is inactive renders a "metric unavailable" state, not an error.
- Widget types: stat, line, bar, pie, table, gauge (apex charts via `leandrocfe/filament-apex-charts`).
- Re-resolves on dashboard date-range change or manual refresh.

## UI

- **Kind**: widget — the render fragment inside a dashboard ([[../../../../architecture/patterns/perceived-performance]] for skeletons).
- **Page**: none of its own; rendered on [[dashboard-builder]]'s canvas and on shared dashboards.
- **Layout**: a card in the grid — header (title), body (chart/stat/table/gauge), footer (last-refreshed).
- **Key interactions**: hover → exact values; manual refresh → re-resolve (skeleton while loading); date-range change (dashboard-level) → all widgets re-resolve.
- **States**: empty (metric returns no data → "no data for this range") · loading (skeleton card) · error/unavailable (unregistered/inactive metric → "metric unavailable") · loaded (rendered chart).
- **Gating**: inherits the dashboard's `analytics.dashboards.view-any`; no separate permission.

## Data

- Owns / writes: nothing (reads `bi_widgets` config; caches resolved data).
- Reads: the metric's registered closure (owning domain's data under `CompanyContext`) via `MetricRegistry`.
- Cross-domain writes: none — pure read-consumer ([[../../../../security/data-ownership]]).

## Relations

- Consumes: metric definitions from [[metric-registry]]; widget config from [[dashboard-builder]].
- Feeds: rendered widgets to [[dashboard-sharing]] (shared read-only view).
- Shared entity: metric keys (owned by source domains, read-only).

## Test Checklist

### Unit
- [ ] Cache key composes as `company:{id}:bi:widget:{widget}:{range}`; a range change yields a distinct key.
- [ ] Unregistered/inactive metric resolves to the "metric unavailable" state, not an exception.

### Feature (Pest)
- [ ] `WidgetDataService::resolve` returns cached data on second call within TTL (no re-aggregation).
- [ ] Manual refresh / date-range change re-resolves and rewrites the cache entry.
- [ ] Each widget type (stat/line/bar/pie/table/gauge) resolves to its expected payload shape.

### Livewire
- [ ] Rendered widget inherits the dashboard's `analytics.dashboards.view-any` gate; no separate permission.
- [ ] Loading state shows a skeleton card (not a spinner); empty result shows "no data for this range".

## Unknowns

- Per-metric TTL vs flat 15 min — see [[../unknowns]].
- Whether tables paginate or cap rows — *(assumed cap)*.

## Related

- [[../_module|Custom Dashboards]] · [[metric-registry]] · [[dashboard-builder]] · [[dashboard-sharing]]
