---
domain: analytics
module: kpi-tracking
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# KPI Tracking

Define key performance indicators with targets, track actuals over time (from a registered metric or manual entry), visualise target-vs-actual, and alert when a KPI falls below threshold.

> Planned for build. Any "shipped/built" language reflects the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

---

## Module-key

`analytics.kpis`

**Priority:** p3
**Panel:** analytics
**Permission prefix:** `analytics.kpis`
**Tables:** `bi_kpis`, `bi_kpi_snapshots`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../dashboards/_module\|analytics.dashboards]] | metric-sourced KPIs read from `MetricRegistry` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, threshold alerts |
| Soft | [[../../projects/okrs/_module\|projects.okrs]] | conceptual sibling — KPIs are metric-driven, OKRs goal-driven |

---

## Core Features

- KPI definition: name, category, metric source (registry key or manual), target value, unit, period (monthly/quarterly)
- Actual value: computed from a registered metric or manually entered
- Target-vs-actual visualisation: gauge + trend line
- Status band: on-target / below-target / above-target (±5% *(assumed)*)
- Categories: revenue, growth, efficiency, customer
- KPI snapshots stored per period for historical trend
- Below-threshold alert, once per period

See feature notes: [[./features/kpi-definition|KPI Definition]] · [[./features/snapshot-capture|Snapshot Capture]] · [[./features/kpi-visualisation|KPI Visualisation]] · [[./features/threshold-alerts|Threshold Alerts]].

---

## Build Manifest

```
database/migrations/xxxx_create_bi_kpis_table.php
database/migrations/xxxx_create_bi_kpi_snapshots_table.php
app/Models/Analytics/{Kpi,KpiSnapshot}.php
app/Data/Analytics/{CreateKpiData,RecordManualValueData}.php
app/Services/Analytics/{KpiService,KpiSnapshotService}.php
app/Console/Commands/Analytics/CaptureKpiSnapshotsCommand.php
app/Filament/Analytics/Resources/KpiResource.php
app/Filament/Analytics/Pages/KpiDashboardPage.php
database/factories/Analytics/KpiFactory.php
tests/Feature/Analytics/KpiTrackingTest.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Snapshot capture idempotent per period; metric-sourced values match registry fixture
- [ ] Status bands at ±5% boundaries
- [ ] Below-threshold alert fires once per period
- [ ] Manual KPI requires manual entry; no auto value

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `MetricRegistry` closures | every active domain (via dashboards) | metric-sourced KPI actuals resolve through the owning domain's closure under `CompanyContext` |
| Feeds | `NotificationService` call | [[../../core/notifications/_module\|core.notifications]] | below-threshold alert dispatched via the notifications service (Analytics does not write notification tables) |
| Soft | conceptual | [[../../projects/okrs/_module\|projects.okrs]] | sibling; no data edge |

**Data ownership:** `analytics.kpis` writes only `bi_kpis`, `bi_kpi_snapshots`. Metric actuals are read via `MetricRegistry`; alerts are sent through the notifications service — never by writing another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[../dashboards/_module|analytics.dashboards]]
- [[../../projects/okrs/_module|projects.okrs]]
- [[../../../security/data-ownership]]
