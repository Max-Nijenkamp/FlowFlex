---
domain: analytics
module: kpi-tracking
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# KPI Tracking — Services & Contracts

No REST API and no domain events in v1. Cross-domain interaction is: **reads** via `MetricRegistry`, **alerts** via the notifications service.

---

## Services & Actions

- `KpiSnapshotService::capture(string $period): CaptureResult` — for each KPI: metric-sourced → resolve via `MetricRegistry` under the owning domain's `CompanyContext`; manual → skip until entered. Upsert one `bi_kpi_snapshots` row per `(kpi, period)`. Fire below-threshold alert once (`alerted` guard).
- `KpiService::status(Kpi $k): string` — on-target / below-target / above-target using the ±5% band *(assumed)*.

---

## Cross-domain contracts

| Direction | Mechanism | Counterpart |
|---|---|---|
| Read metric actuals | `MetricRegistry::get(key)->resolver(range, filters)` | owning domain (via [[../dashboards/_module\|dashboards]]) |
| Send alert | `NotificationService::notify(...)` (service call) | [[../../core/notifications/_module\|core.notifications]] |

Analytics never writes another domain's tables: metric reads run inside the owning domain's closure; alerts are handed to the notifications service which owns its own tables ([[../../../security/data-ownership]]).

---

## Events

None fired, none consumed. The threshold alert is a **service call**, not a broadcast domain event.

See [[data-model]], [[security]], [[./features/threshold-alerts|Threshold Alerts feature]].
