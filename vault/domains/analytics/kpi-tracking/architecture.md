---
domain: analytics
module: kpi-tracking
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `KpiResource` | #1 CRUD resource | tweaks: `custom-header-actions` (record manual value) *(assumed)* | define KPIs; manual-source KPIs get a "record value" action gated by `record-values` |
| `KpiDashboardPage` | #6 Dashboard custom page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | gauges + trend lines (apex charts); satisfies [[../../../architecture/patterns/custom-page-checklist]] |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('analytics.kpis.view-any') && BillingService::hasModule('analytics.kpis')`
per [[../../../architecture/filament-patterns]] #1. `KpiDashboardPage` is a custom page — Filament does not auto-gate custom pages, so it MUST declare `canAccess()` explicitly. Public/portal surfaces would declare a guest or scoped-portal guard instead (Vue+Inertia per [[../../../architecture/ui-strategy]]); Analytics has none.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| KPI definition CRUD + manual actual entry (`RecordManualValueData`) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` conflict notification with Reload action ([[../../../architecture/patterns/optimistic-locking]]) |
| Snapshot capture (`KpiSnapshotService::capture`) | Atomic | Idempotent upsert on unique `(kpi_id, period)` + `bi_kpi_snapshots.alerted` once-guard — a re-run or concurrent capture cannot double-write a snapshot or double-fire the below-threshold alert |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]]. The scheduled snapshot capture is not ordinary CRUD; its unique-key upsert + `alerted` once-guard is the atomic guard that makes it safe under retries and overlapping runs.

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
