---
domain: analytics
module: dashboards
feature: metric-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# MetricRegistry

The infrastructure that lets Analytics read every other domain's numbers **without ever touching their tables**. Domains register named, pre-aggregated, CompanyScope-safe metric closures; Analytics resolves widgets and KPIs against them.

## Behaviour

- Each domain calls `MetricRegistry::register($key, MetricDefinition)` in its own service provider at boot.
- `MetricDefinition` wraps a resolver closure `fn(DateRange, filters): array` that runs under the owning domain's `CompanyContext` and reads only that domain's models.
- `MetricRegistry::available()` returns registered metrics filtered by `BillingService::hasModule(...)` — inactive modules' metrics disappear from every picker.
- Keys follow `{domain}.{metric}` (e.g. `crm.pipeline.value`, `finance.revenue.mtd`, `hr.headcount`).
- This feature is pure infrastructure — no screen of its own; it powers the builder, widget rendering, KPIs, and data-views.

## UI

- **Kind**: background — no page. It is a singleton service populated at boot; consumed by [[dashboard-builder]], [[widget-rendering]], and the KPI / data-view modules.
- **Page**: none.
- **Layout**: n/a.
- **Key interactions**: n/a (registration happens in code; the widget picker that *surfaces* it lives in [[dashboard-builder]]).
- **States**: n/a.
- **Gating**: metrics are exposed only for modules the company has active (`hasModule`); no direct permission of its own.

## Data

- Owns / writes: nothing — it is an in-memory registry; the only persisted artifact is a widget's `data_source.metric_key` in `bi_widgets`.
- Reads: invokes each owning domain's registered closure (which reads that domain's tables under its own scope).
- Cross-domain writes: none, ever. The registry is the read-only bridge; Analytics never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: registration calls from **every active domain** (CRM, Finance, HR, Projects, …) — each supplies its own metric closures.
- Feeds: metric definitions to [[dashboard-builder]] (picker), [[widget-rendering]] (resolution), and `analytics.kpis` / `analytics.data-views`.
- Shared entity: none persisted; metric *keys* are the shared vocabulary.

## Unknowns

- `*(assumed)*` — registry is a singleton, not an Interface→Service binding, for v1.
- Whether a metric can declare a native TTL (vs the flat 15-min widget cache) is unresolved — see [[../unknowns]].

## Related

- [[../_module|Custom Dashboards]] · [[dashboard-builder]] · [[widget-rendering]] · [[../../../../security/data-ownership]]
