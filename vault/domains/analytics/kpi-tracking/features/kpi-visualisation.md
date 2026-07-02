---
domain: analytics
module: kpi-tracking
feature: kpi-visualisation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# KPI Visualisation

The KPI dashboard: gauges for target-vs-actual and trend lines across periods.

## Behaviour

- Renders each KPI as a gauge (current actual vs target) coloured by status band (on/below/above).
- Trend line per KPI from its `bi_kpi_snapshots` history.
- Grouped/filterable by category.
- Reads persisted snapshots (fast) plus the live current value.

## UI

- **Kind**: custom-page (dashboard) — apex gauges + line charts ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `KpiDashboardPage` at `/analytics/kpis/dashboard` *(route assumed)*.
- **Layout**: grid of KPI gauge cards grouped by category; click a card → trend line + snapshot table below/slide-over; category filter in the header.
- **Key interactions**: select category → filter cards; click a KPI → expand trend + history; hover gauge → exact value + delta vs target.
- **States**: empty (no KPIs → CTA to define one) · loading (skeleton gauges) · error (chart data fails → retry) · selected (KPI card highlighted, trend shown).
- **Gating**: visible with `analytics.kpis.view-any`.

## Data

- Owns / writes: nothing (read-only render of `bi_kpis` + `bi_kpi_snapshots`).
- Reads: own tables; live current value via `MetricRegistry` for metric-sourced KPIs.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: snapshots from [[snapshot-capture]]; definitions from [[kpi-definition]]; live values from [[../../dashboards/features/metric-registry|MetricRegistry]].
- Feeds: nothing downstream. A KPI gauge can also appear as a dashboard widget (via a KPI-gauge widget type in [[../../dashboards/_module|dashboards]]).
- Shared entity: metric keys (read-only).

## Test Checklist

### Unit
- [ ] Gauge status colour maps to the status band; trend series ordered by period

### Feature (Pest)
- [ ] Dashboard reads only the active company's KPIs + snapshots
- [ ] Live current value resolves via `MetricRegistry` for metric-sourced KPIs

### Livewire
- [ ] `KpiDashboardPage` renders gauge grid + category filter; empty state CTA when no KPIs
- [ ] Denied without `analytics.kpis.view-any`; one failed chart shows its error card without blanking the page

## Unknowns

- Whether the KPI dashboard is a distinct page or a seeded dashboard template — *(assumed: distinct custom page)*. See [[../unknowns]].

## Related

- [[../_module|KPI Tracking]] · [[snapshot-capture]] · [[kpi-definition]] · [[../../dashboards/_module|Custom Dashboards]]
