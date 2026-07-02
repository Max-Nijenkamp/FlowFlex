---
domain: analytics
module: dashboards
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Custom Dashboards — Architecture

## The MetricRegistry (the domain's core infrastructure)

Analytics never queries another domain's tables. Instead every domain **registers metrics** in its own service provider:

- `MetricRegistry::register(string $key, MetricDefinition $def)` — `$def` wraps a closure that returns pre-aggregated numbers under `CompanyContext` (so it always runs through the owning domain's own scope). Example keys: `crm.pipeline.value`, `finance.revenue.mtd`, `hr.headcount`.
- `MetricRegistry::available(): Collection` — filtered by `BillingService::hasModule(...)` so only metrics of active modules are offered in the widget picker.
- `WidgetDataService::resolve(Widget $w, DateRange $range): array` — resolves the widget's `metric_key` against the registry, applies filters + range, returns cached data.

Because the closure lives in the owning domain and reads only that domain's models, the data-ownership boundary ([[../../../security/data-ownership]]) holds automatically — Analytics is a pure read-consumer.

---

## Services & Actions

- `MetricRegistry` / `MetricDefinition` (`app/Support/Analytics/`) — registration + module-filtered listing
- `WidgetDataService::resolve(Widget, DateRange): array` — cached metric resolution
- No Interface→Service split needed for v1 (registry is a singleton, WidgetDataService is a plain service) *(assumed)*

---

## Events

None fired, none consumed. Analytics reacts to nothing; it reads on demand via the registry. (KPI capture — a separate module — is schedule-driven, not event-driven.)

---

## Filament Artifacts

**Nav group:** Dashboards

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DashboardBuilderPage` | #6 Dashboard custom page | [[../../../architecture/patterns/page-blueprints#Dashboard]] (+ drag-drop grid builder extension *(assumed)*) | drag-drop grid (Livewire + Alpine), widget picker filtered by active modules; satisfies [[../../../architecture/patterns/custom-page-checklist]] |
| `DashboardResource` | #1 CRUD resource | tweaks: `custom-header-actions` (share toggle) *(assumed)* | list/manage dashboards, share/private badge column |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('analytics.dashboards.view-any') && BillingService::hasModule('analytics.dashboards')`
per [[../../../architecture/filament-patterns]] #1. `DashboardBuilderPage` is a custom page — Filament does not auto-gate custom pages, so it MUST declare `canAccess()` explicitly. Public/portal surfaces would declare a guest or scoped-portal guard instead (Vue+Inertia per [[../../../architecture/ui-strategy]]); Analytics has none.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Dashboard + widget CRUD (builder: layout, widget add/remove/resize, share toggle) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` "record changed" conflict notification with Reload action ([[../../../architecture/patterns/optimistic-locking]]) |
| Widget data resolution (`WidgetDataService::resolve`) | n/a | Read-only — resolves cached metric closures, writes nothing |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]]. The builder is the only shared editable surface here; a shared dashboard edited concurrently by owner + `manage-shared` holder is exactly the stale-write case the optimistic tier covers.

---

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:bi:widget:{widget}:{range}` | 15 min *(assumed)* | TTL only |

Heavy aggregation cost lives in the metric closures — see [[../../../architecture/caching]] and [[../../../architecture/performance]].

---

## Search & Realtime

- Search: none (dashboards are not indexed in Meilisearch for v1 *(assumed)*).
- Realtime: none — widgets refresh on load / date-range change / manual refresh; no Reverb push in v1 *(assumed)*.

---

## Security Notes

See [[./security]] for the full access contract, permissions, and tenant isolation. The critical control: the widget `data_source` JSON is **validated against the registry** on write — an unregistered or inactive-module metric key is rejected, so a widget can never smuggle a free-form query.
