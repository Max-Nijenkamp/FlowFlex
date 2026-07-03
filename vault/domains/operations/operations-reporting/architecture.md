---
domain: operations
module: operations-reporting
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Operations Reporting — Architecture

## Services & Actions

`OperationsAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): OperationsMetricsData` — a set of aggregate queries (valuation, movement series, spend tables, turnover/dead-stock lists) run with eager loading / grouped queries (no N+1), money via brick/money. Reuses `StockService::valuation` for the valuation figure.

Owns no tables — every figure is derived from other Operations modules' data, read-only.

---

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:operations:metrics:{from}:{to}` | 1 h historical / 15 min current window | TTL only |

Historical windows (closed date ranges) cache longer; the current period caches short. See [[../../../architecture/caching]].

---

## Events

Fires none, consumes none. (A future optimisation could invalidate the current-window cache on `GoodsReceived` / stock movements, but v1 is TTL-only.)

---

## Filament Artifacts

**Nav group:** Reporting

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `OperationsDashboardPage` | #6 dashboard page + apex charts | date filter; Excel export; soft-dep sections conditional on active modules |
| `ValuationWidget` / `MovementTrendWidget` / `SpendWidget` / `DeadStockWidget` | #6 widgets | composed on the dashboard |

**Access contract:** `canAccess() = Auth::user()->can('operations.reporting.view') && BillingService::hasModule('operations.reporting')` per [[../../../architecture/filament-patterns]] #1 — the custom page states it explicitly.

**Security note** ([[../../../_archive/build-history/security-audit-2026-06-11]]): rate-limit the Excel export action per user/company.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| All dashboard/widget/export paths | n-a | Read-only aggregation over other Operations modules' tables; owns no tables |
| Redis aggregate cache writes | n-a | TTL-keyed cache set; idempotent recompute, last-write-wins safe |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

- No search index (read-only aggregation).
- No realtime — dashboards read cached metrics on load / date-filter change.
