---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.forecasting
status: planned
priority: v1
depends-on: [crm.deals, core.billing, core.rbac]
soft-depends: [finance.forecasting]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: [crm_quotas, crm_forecast_snapshots]
permission-prefix: crm.forecasting
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Sales Forecasting

Sales forecasts, quota tracking, and weighted pipeline. Predict revenue and measure reps against targets.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/deals\|crm.deals]] | value, probability, stage, close dates |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/finance/forecasting\|finance.forecasting]] | driver input to financial forecast |

---

## Core Features

- Quota: per rep, per team, per period (monthly/quarterly)
- Weighted pipeline: deal value × stage probability
- Forecast categories: commit, best-case, pipeline, closed — rep tags open deals *(assumed: `forecast_category` column added to crm_deals by this module)*
- Forecast vs quota: attainment % per rep and team
- Forecast roll-up: rep → team → company
- Historical accuracy: forecast vs actual close (weekly snapshots)
- Pipeline coverage ratio (pipeline value / quota)
- Trend: forecast movement week over week

---

## Data Model

### crm_quotas

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| owner_id | ulid FK users | rep (team roll-up computed) |
| period | string | `YYYY-MM` or `YYYY-Qn`, unique `(company_id, owner_id, period)` |
| quota_cents | bigint | |
| currency | string(3) | |

### crm_forecast_snapshots

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| owner_id | ulid FK | |
| period | string | |
| category | string | commit / best-case / pipeline / closed |
| amount_cents | bigint | |
| captured_at | timestamp | weekly snapshot job |

Reads from `crm_deals` (+ `forecast_category` column this module adds).

---

## DTOs

### SetQuotaData — owner_id, period (format), quota_cents (min:0)
### ForecastData (output) — per rep: quota_cents, closed_cents, commit_cents, best_case_cents, weighted_pipeline_cents, attainment_percent, coverage_ratio

## Services & Actions

- `SalesForecastService::forecast(string $period, ?string $ownerId = null): ForecastData` — live computation from deals; roll-up when ownerId null
- `SetForecastCategoryAction::run(string $dealId, string $category): void` — open deals only
- `CaptureForecastSnapshotsCommand` — weekly per rep/category

---

## Filament

**Nav group:** Pipeline

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `QuotaResource` | #1 CRUD resource | per rep/period |
| `ForecastPage` | #6 dashboard page + apex charts | attainment, coverage, week-over-week trend from snapshots |
| `ForecastWidget` | #6 widget | team attainment summary |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.forecasting.view-any') && BillingService::hasModule('crm.forecasting')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`crm.forecasting.view-own` · `crm.forecasting.view-team` · `crm.forecasting.manage-quotas` · `crm.forecasting.set-category`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CaptureForecastSnapshotsCommand` | crm *(assumed queue: default)* | weekly Mon 06:00 | upsert per `(owner, period, category, week)` |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Weighted pipeline = Σ value × probability (brick/money fixtures)
- [ ] Attainment % per rep + roll-up math
- [ ] Category settable on open deals only
- [ ] Snapshot command idempotent per week
- [ ] view-own vs view-team scoping

---

## Build Manifest

```
database/migrations/xxxx_create_crm_quotas_table.php
database/migrations/xxxx_create_crm_forecast_snapshots_table.php
database/migrations/xxxx_add_forecast_category_to_crm_deals.php
app/Models/CRM/{Quota,ForecastSnapshot}.php
app/Data/CRM/{SetQuotaData,ForecastData}.php
app/Services/CRM/SalesForecastService.php
app/Actions/CRM/SetForecastCategoryAction.php
app/Console/Commands/CRM/CaptureForecastSnapshotsCommand.php
app/Filament/CRM/Resources/QuotaResource.php
app/Filament/CRM/Pages/ForecastPage.php
app/Filament/CRM/Widgets/ForecastWidget.php
database/factories/CRM/QuotaFactory.php
tests/Feature/CRM/SalesForecastTest.php
```

---

## Related

- [[domains/crm/deals]]
- [[domains/crm/pipeline]]
- [[domains/finance/forecasting]]
