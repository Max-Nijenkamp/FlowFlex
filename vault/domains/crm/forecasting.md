---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.forecasting
status: planned
color: "#4ADE80"
---

# Sales Forecasting

Sales forecasts, quota tracking, and weighted pipeline. Predict revenue and measure reps against targets.

## Core Features

- Quota: per rep, per team, per period (monthly/quarterly)
- Weighted pipeline: deal value × stage probability
- Forecast categories: commit, best-case, pipeline, closed
- Forecast vs quota: attainment % per rep and team
- Forecast roll-up: rep → team → company
- Historical accuracy: forecast vs actual close
- Pipeline coverage ratio (pipeline value / quota)
- Trend: forecast movement week over week

## Data Model

| Table | Key Columns |
|---|---|
| `crm_quotas` | company_id, owner_id (rep/team), period, quota_cents |
| `crm_forecast_snapshots` | company_id, owner_id, period, category, amount_cents, captured_at |

Reads from `crm_deals` (value, probability, stage, close date).

## Filament

**Nav group:** Pipeline

- `QuotaResource` — set quotas per rep/team/period
- `ForecastPage` (custom page) — weighted pipeline + quota attainment charts
- `ForecastWidget` — team attainment summary

## Cross-Domain

- Reads deals; revenue actuals reconcile with Finance

## Related

- [[domains/crm/deals]]
- [[domains/crm/pipeline]]
- [[domains/finance/forecasting]]
