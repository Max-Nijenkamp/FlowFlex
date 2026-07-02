---
domain: analytics
module: dashboards
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Custom Dashboards — Registry, Services & Contracts

The Analytics domain has **no REST API and no events** in v1. Its "API" is the internal
`MetricRegistry` contract that every other domain registers against. This file documents that
contract and the widget-resolution service.

---

## MetricRegistry (the cross-domain read contract)

`app/Support/Analytics/MetricRegistry` — a singleton populated at boot from each domain's service provider.

- `register(string $key, MetricDefinition $def): void` — an owning domain registers a named metric. `$def` wraps a closure returning **pre-aggregated numbers under `CompanyContext`** (runs through the owning domain's own scope/models). Example keys: `crm.pipeline.value`, `finance.revenue.mtd`, `hr.headcount`.
- `available(): Collection` — all registered metrics **filtered by `BillingService::hasModule(...)`**, so only metrics of currently-active modules appear in the widget picker.
- `get(string $key): ?MetricDefinition` — resolve one metric; returns null if unregistered.

### MetricDefinition
| Field | Type | Notes |
|---|---|---|
| key | string | globally unique, `{domain}.{metric}` convention |
| label | string | shown in the widget picker |
| module_key | string | the module this metric belongs to (drives `hasModule` filter) |
| allowed_filters | array | filter keys the metric accepts (validates `AddWidgetData.filters`) |
| resolver | closure | `fn(DateRange $range, array $filters): array` — runs in the owning domain, CompanyScope-safe |

The resolver closure is the **only** way Analytics touches another domain's data, and it lives in that
domain's code — so the data-ownership boundary ([[../../../security/data-ownership]]) holds by construction.

---

## Services & Actions

- `WidgetDataService::resolve(Widget $w, DateRange $range): array` — resolves the widget's `metric_key` against the registry, applies `filters` + `range`, returns **cached** data (`company:{id}:bi:widget:{widget}:{range}`, TTL 15 min *(assumed)*). Rejects a widget whose metric is unregistered or whose module is inactive.
- No Interface→Service split for v1 — registry is a singleton, `WidgetDataService` a plain service *(assumed)*.

---

## Events

None fired, none consumed. Analytics reads on demand via the registry; it reacts to nothing.
KPI snapshot capture (a sibling module) is schedule-driven, not event-driven.

See [[data-model]], [[security]], [[./features/metric-registry|MetricRegistry feature]].
