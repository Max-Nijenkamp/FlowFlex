---
domain: analytics
module: kpi-tracking
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# KPI Tracking — Architecture

## Two value sources

A KPI's actual comes from one of two sources, recorded in `bi_kpis.metric_source` (jsonb):

- **metric** — `{ type: metric, key }` → resolved via the [[../dashboards/features/metric-registry|MetricRegistry]] under the owning domain's `CompanyContext`. Analytics reads, never touches source tables.
- **manual** — `{ type: manual }` → a user records the actual each period via `RecordManualValueData`.

## Snapshot model

Per period, `KpiSnapshotService::capture(period)` upserts one `bi_kpi_snapshots` row per KPI: metric-sourced KPIs are resolved from the registry; manual ones are skipped until entered. The snapshot freezes `actual_value` + `target_value` for historical trend, and carries an `alerted` once-guard.

---

## Services & Actions

- `KpiSnapshotService::capture(string $period): CaptureResult` — resolve/skip per source; upsert per `(kpi, period)`; fire the below-threshold alert once.
- `KpiService::status(Kpi $k): string` — on-target / below / above using the ±5% band *(assumed)*.
- No Interface→Service split for v1 — plain services *(assumed)*.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CaptureKpiSnapshotsCommand` | default | monthly 1st / quarterly | upsert per `(kpi, period)`; alert `alerted` once-guard |

---

## Events

None fired as domain events. Threshold breach is delivered via the **notifications service** (a service call), not a cross-domain event — Analytics stays a leaf that reads metrics and requests notifications.

---

## Filament Artifacts

**Nav group:** KPIs

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `KpiResource` | simple-resource | define KPIs, manual value entry |
| `KpiDashboardPage` | custom-page (dashboard) | gauges + trend lines (apex charts) |

**Access contract:** `canAccess() = Auth::user()->can('analytics.kpis.view-any') && BillingService::hasModule('analytics.kpis')` per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly.

---

## Caching

KPI actuals inherit the underlying metric's cache (via `MetricRegistry`); snapshots are persisted, so the KPI dashboard reads rows, not live aggregations. No separate KPI cache in v1 *(assumed)*.

---

## Search & Realtime

- Search: none.
- Realtime: none — the dashboard reflects the latest captured snapshot; no live push *(assumed)*.

---

## Security Notes

See [[./security]]. Metric-sourced KPIs can only reference a **registered, active-module** metric (rejected otherwise); alerts go through the notifications service; both `bi_` tables are CompanyScope-bound.
