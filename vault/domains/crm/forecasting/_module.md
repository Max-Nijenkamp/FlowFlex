---
domain: crm
module: forecasting
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# CRM Forecasting

Sales forecasts, quota tracking, and weighted pipeline. Predict revenue and measure reps against targets.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module Key

```
module-key:        crm.forecasting
priority:          v1
panel:             crm
permission-prefix: crm.forecasting
tables:            crm_quotas, crm_forecast_snapshots
```

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../deals/_module\|Deals]] | Source of deal value, probability, stage, and close dates for weighted pipeline. |
| Hard | [[../../../infrastructure/module-catalog\|core.billing]] | Module gating (`hasModule`). |
| Hard | [[../../../security/authn-authz\|core.rbac]] | Permissions and role scoping. |
| Soft | [[../../finance/forecasting/_module\|finance.forecasting]] | Sales forecast is a driver input into the financial forecast. |

## Core Features

- Quota per rep / team / period (monthly or quarterly).
- Weighted pipeline (deal value × stage probability).
- Forecast categories commit / best-case / pipeline / closed — rep tags open deals *(assumed: `forecast_category` column added to `crm_deals` by this module)*.
- Forecast vs quota attainment % per rep and per team.
- Forecast roll-up rep → team → company.
- Historical accuracy: forecast vs actual close (weekly snapshots).
- Pipeline coverage ratio (pipeline value / quota).
- Trend: forecast movement week over week.

## See features/

- [[features/weighted-pipeline|Weighted pipeline]]
- [[features/forecast-categories|Forecast categories]]

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

## Test Checklist

- [ ] Tenant isolation + module gating enforced.
- [ ] Weighted pipeline = Σ value × probability (brick/money fixtures).
- [ ] Attainment % per rep + roll-up math correct.
- [ ] Forecast category settable on open deals only.
- [ ] Snapshot command idempotent per week.
- [ ] `view-own` vs `view-team` scoping enforced.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | read query | crm.deals | Deal value, probability, stage, close date, owner — source for weighted pipeline. Read-only. |
| Reads | read query | crm.pipeline | Stage → probability (source of truth in pipeline). Read-only. |
| Consumes | `DealWon` / `DealLost` | crm.deals | Refresh cached forecast projections *(assumed — only if a cached projection is kept; otherwise pure read-on-demand, consumes nothing)*. |
| Fires | — | — | Read/aggregate module — fires no domain events. Sales forecast is a read-API input into finance.forecasting, not an event. |

> [!warning] UNVERIFIED — `forecast_category` on `crm_deals`
> The spec adds a `forecast_category` column to `crm_deals` (owned by crm.deals) via this module's migration. That is a cross-domain write and violates the ownership rule. Resolve via ADR: deals owns the column (forecasting sets it through a deals action/event) or forecasting stores category in its own table keyed by `deal_id`.

**Data ownership:** `crm.forecasting` writes only `crm_quotas` and `crm_forecast_snapshots`; it never writes `crm_deals`. All cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../deals/_module|Deals]]
- [[../pipeline/_module|Pipeline]]
- [[../../finance/forecasting/_module|Finance Forecasting]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../glossary]]
