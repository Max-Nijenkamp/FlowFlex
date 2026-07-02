---
domain: analytics
module: dashboards
feature: dashboard-builder
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Dashboard Builder

Drag-and-drop canvas where a user composes a dashboard from widgets, each bound to a registered metric. The flagship surface of the Analytics panel.

## Behaviour

- Create a dashboard (name, private/shared), then add widgets onto a resizable grid.
- Widget picker lists only metrics from `MetricRegistry::available()` — i.e. active modules only.
- Adding a widget requires choosing a `type` (stat/line/bar/pie/table/gauge), a `metric_key`, and filters; the `data_source` JSON is validated against the registry on save.
- Widgets can be dragged, resized, and repositioned; layout persists to `bi_dashboards.layout` / `bi_widgets.position`.
- A dashboard-wide date-range filter re-resolves every widget.
- Cross-domain composition: e.g. a CRM pipeline stat next to a Finance revenue chart on one canvas — each reads its own domain's closure.

## UI

- **Kind**: custom-page — bespoke drag-and-drop grid ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `DashboardBuilderPage` at `/analytics/dashboards/{dashboard}/build` *(route assumed)* — custom Filament page (Livewire + Alpine grid).
- **Layout**: main canvas = resizable widget grid; right rail = widget picker grouped by domain (module-filtered); top bar = dashboard name, date-range filter, share toggle, save.
- **Key interactions**: drag widget from picker → drop on grid → configure metric + filters in a slide-over; drag/resize placed widgets → optimistic layout update → persist; change date range → all widgets re-resolve.
- **States**: empty (no widgets → "add your first widget" CTA) · loading (skeleton grid cells while metrics resolve) · error (metric rejected on save → inline validation; unavailable metric → widget shows "metric unavailable") · selected (widget outlined, config slide-over open).
- **Gating**: visible with `analytics.dashboards.view-any`; editing requires `analytics.dashboards.update-own`; creating requires `analytics.dashboards.create`.

## Data

- Owns / writes: `bi_dashboards`, `bi_widgets` (this module's tables only).
- Reads: `MetricRegistry` closures for live widget previews (each reads its owning domain's data).
- Cross-domain writes: none — Analytics only writes its own two tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: metric definitions from [[metric-registry]] (which every active domain populates).
- Feeds: dashboards into [[dashboard-sharing]] and [[widget-rendering]]; a saved dashboard is a schedulable source for `analytics.exports`.
- Shared entity: none owned elsewhere; metric keys reference other domains' registered metrics.

## Unknowns

- Exact route/slug and whether build + view are one page or two — *(assumed)*.
- Layout source-of-truth (dashboard.layout vs widget.position) — see [[../unknowns]].

## Related

- [[../_module|Custom Dashboards]] · [[metric-registry]] · [[widget-rendering]] · [[dashboard-sharing]]
