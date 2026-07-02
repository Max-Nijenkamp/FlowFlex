---
domain: analytics
module: data-views
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Cross-Domain Data Views — Architecture

## The DataView contract

A data view is a FlowFlex-shipped class, not a user-built query. Each implements a small contract:

- `requiredModules(): array` — module keys that must all be active for the view to appear.
- `run(DateRange $range): DataViewResult` — composes the aggregate by calling each **source domain's own read path** (its query service / registered closure) under `CompanyContext`, then joins/aggregates in memory or via a CompanyScope-safe query. Never touches another domain's tables directly.
- `drillDown(array $key, DateRange $range): DataViewResult` — expands one aggregate row into its underlying records, again through the source domains' read paths.

`DataViewRegistry::register(class-string $view)` / `available(): Collection` — the registry lists shipped views filtered by `BillingService::hasModule(...)` across every entry in `requiredModules()`.

Because the aggregation logic lives in Analytics but the **data access** goes through each owning domain's read path, the data-ownership boundary ([[../../../security/data-ownership]]) holds — Analytics is a pure read-consumer, same as [[../dashboards/_module|dashboards]].

---

## Services & Actions

- `DataViewRegistry` (`app/Support/Analytics/`) — register + module-filtered listing
- `DataViewContract` — `requiredModules()`, `run()`, `drillDown()`
- `DataViewResult` — output DTO (columns, rows, drill targets)
- No Interface→Service split for v1 — views are plain classes, registry a singleton *(assumed)*

---

## Events

None fired, none consumed. Views resolve on demand.

---

## Filament Artifacts

**Nav group:** Data Views

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DataViewsPage` | #17 Gallery / directory | [[../../../architecture/patterns/page-blueprints#Gallery / Directory]] | available views as cards, module-filtered |
| Per-view render | #9 Report builder / query UI | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — result pane + drill-down; builder rail reduced to date-range (views are shipped, not user-built) | charts + drill-down table + export, inside the same page |

**Access contract (mandatory):** `canAccess() = Auth::user()->can('analytics.data-views.view-any') && BillingService::hasModule('analytics.data-views')` per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| — | n/a | Read-only module: views aggregate other domains' read paths; no writes beyond cache entries. Nothing to stale-check ([[../../../decisions/decision-2026-07-02-optimistic-locking-standard]]) |

---

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:bi:view:{view}:{range}` | 1 h *(assumed)* | TTL only |

Heavy join/aggregation cost is memoised per `(view, range)`. See [[../../../architecture/caching]], [[../../../architecture/performance]].

---

## Search & Realtime

- Search: none.
- Realtime: none — views recompute on load / date-range change / manual refresh *(assumed)*.

---

## Security Notes

See [[./security]]. The critical control: a view only appears when **all** its `requiredModules()` are active, and every source read runs under the owning domain's `CompanyContext` — no cross-company leakage, no free-form query. Export is rate-limited ([[../../../architecture/security]]).
