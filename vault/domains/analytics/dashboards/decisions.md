---
domain: analytics
module: dashboards
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Custom Dashboards — Decisions

---

## ADR: MetricRegistry as the sole cross-domain read path

Analytics never queries another domain's tables. Instead every domain registers **named,
pre-aggregated, CompanyScope-safe metrics** in its own service provider, and Analytics resolves
widgets against that registry. This keeps the data-ownership boundary
([[../../../security/data-ownership]]) intact by construction: the aggregation closure lives in the
owning domain and reads only that domain's models. Dashboards is the **anchor module** of Analytics —
it must build first because it ships the registry every other Analytics module reads from.

---

## Build Order: Dashboards First

`analytics.dashboards` has no intra-domain hard dependency and ships the `MetricRegistry` +
`WidgetDataService` infrastructure. `analytics.kpis` and `analytics.data-views` hard-depend on it.
Build sequence within the domain: **dashboards → kpis / data-views → reports → exports**.

---

## Widget picker filtered by module activation

`MetricRegistry::available()` filters through `BillingService::hasModule(...)`, so deactivating a
module immediately removes its metrics from the picker **and** its existing widgets stop resolving
(rendered as an empty/"metric unavailable" state rather than erroring). This mirrors the
module-scoped-permissions rule from [[../../../decisions/decision-2026-06-20-full-mapping-conventions]].

---

## Implementation Notes

- Registry is a singleton; `WidgetDataService` a plain service — no Interface→Service split needed for v1 *(assumed)*.
- Widget data cached per `(widget, range)`, TTL 15 min *(assumed)* — heavy aggregation cost lives in the owning domain's closure.
- Drag-and-drop grid is Livewire + Alpine on a custom Filament page ([[../../../architecture/patterns/custom-pages]]); no Vue.
- Seeded templates (Sales / HR / Finance Overview) compose only metrics whose modules are active for the company.
