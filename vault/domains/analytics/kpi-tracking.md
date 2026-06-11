---
type: module
domain: Analytics & BI
domain-key: analytics
panel: analytics
module-key: analytics.kpis
status: planned
priority: p3
depends-on: [analytics.dashboards, core.billing, core.rbac, core.notifications]
soft-depends: [projects.okrs]
fires-events: []
consumes-events: []
patterns: [custom-pages, queues]
tables: [bi_kpis, bi_kpi_snapshots]
permission-prefix: analytics.kpis
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# KPI Tracking

Define key performance indicators with targets, track actuals over time, and visualise progress against goals.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/analytics/dashboards\|analytics.dashboards]] | metric sources come from `MetricRegistry` |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, threshold alerts |
| Soft | [[domains/projects/okrs\|projects.okrs]] | conceptual sibling — KPIs are metric-driven, OKRs goal-driven |

---

## Core Features

- KPI definition: name, metric source (registry key or manual), target value, unit, period (monthly/quarterly)
- Actual value: computed from a registered metric or manually entered
- Target vs actual visualisation: gauge, trend line
- Status: on-target / below-target / above-target (±5% band *(assumed)*)
- KPI categories (revenue, growth, efficiency, customer)
- Trend over multiple periods
- KPI snapshots stored per period for historical comparison
- Alert when KPI falls below threshold (once per period)

---

## Data Model

### bi_kpis — id, company_id (indexed), name, category, metric_source (jsonb: {type: metric/manual, key?}), target_value decimal(16,2), unit, period (monthly/quarterly), owner_id, deleted_at
### bi_kpi_snapshots — id, kpi_id FK, company_id, period_label (unique per kpi), actual_value, target_value, captured_at; `alerted` bool (once-guard)

---

## DTOs

### CreateKpiData — name, category (in set), metric_source (registered key + active module, or manual), target_value, unit, period
### RecordManualValueData — kpi_id (manual source), period_label, actual_value

## Services & Actions

- `KpiSnapshotService::capture(string $period): CaptureResult` — metric-sourced KPIs resolved via MetricRegistry; manual ones skipped until entered; upsert per (kpi, period)
- `KpiService::status(Kpi $k): string`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CaptureKpiSnapshotsCommand` | default | monthly 1st / quarterly | upsert per (kpi, period); alert once-guard |

---

## Filament

**Nav group:** KPIs

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `KpiResource` | #1 CRUD resource | targets, manual value entry |
| `KpiDashboardPage` | #6 dashboard page | gauges + trends (apex) |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('analytics.kpis.view-any') && BillingService::hasModule('analytics.kpis')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`analytics.kpis.view-any` · `analytics.kpis.manage` · `analytics.kpis.record-values`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Snapshot capture idempotent per period; metric-sourced values match registry fixture
- [ ] Status bands at ±5% boundaries
- [ ] Below-threshold alert once per period
- [ ] Manual KPI requires manual entry; no auto value

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

## Related

- [[domains/analytics/dashboards]]
- [[domains/projects/okrs]]
