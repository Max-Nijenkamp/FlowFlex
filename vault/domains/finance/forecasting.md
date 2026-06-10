---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.forecasting
status: planned
priority: v1
depends-on: [finance.ledger, finance.budgets, core.billing, core.rbac]
soft-depends: [crm.forecasting, hr.workforce, finance.cashflow]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: [fin_forecasts, fin_forecast_lines]
permission-prefix: finance.forecasting
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Forecasting

Financial forecasting, scenario modelling, and variance analysis. Absorbed from the former FP&A domain.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | historical actuals base |
| Hard | [[domains/finance/budgets\|finance.budgets]] | three-way comparison |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/crm/forecasting\|crm.forecasting]], [[domains/hr/workforce-planning\|hr.workforce]] | driver inputs (pipeline, headcount); manual drivers without them |
| Soft | [[domains/finance/cash-flow\|finance.cashflow]] | cash position projection link |

---

## Core Features

- Rolling forecast: projected revenue/expense per period, updated monthly
- Forecast models: based on historical actuals + growth assumptions
- Scenario comparison: base / optimistic / pessimistic side by side
- Driver-based forecasting: model revenue from drivers (headcount, deals, units) — driver values manual or pulled from soft-dep modules
- Forecast vs actual vs budget three-way comparison
- Assumptions register: documented assumptions per forecast (jsonb)
- Forecast accuracy tracking over time (projected vs realised per closed period)
- Cash position projection (links Cash Flow)

---

## Data Model

### fin_forecasts

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| scenario | string | base / optimistic / pessimistic |
| fiscal_year | int | |
| assumptions | jsonb | [{key, description, value}] |
| deleted_at | timestamp nullable | |

### fin_forecast_lines

| Column | Type | Notes |
|---|---|---|
| id, forecast_id FK, company_id | ulid | |
| account_id | ulid FK fin_accounts | |
| period | string | `YYYY-MM`, unique `(forecast_id, account_id, period)` |
| projected_cents | bigint | |

---

## DTOs

### CreateForecastData — name, scenario (in set), fiscal_year, assumptions[], lines[{account_id, period, projected_cents}]
### ForecastComparisonData (output) — per account/period: projected_cents, actual_cents, budgeted_cents, variances

## Services & Actions

- `ForecastService::create(CreateForecastData $data)` / `seedFromActuals(string $forecastId, float $growthPercent): void` — copies trailing-12m actuals × growth
- `ForecastService::comparison(string $forecastId, ?string $period): ForecastComparisonData`
- `ForecastService::accuracy(string $forecastId): float` — MAPE over closed periods *(assumed)*

---

## Filament

**Nav group:** Planning

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ForecastResource` | #1 CRUD resource | scenario + assumptions editor, seed-from-actuals action |
| `ForecastComparisonPage` | #9 report custom page + apex charts | scenario side-by-side, three-way comparison |

---

## Permissions

`finance.forecasting.view-any` · `finance.forecasting.create` · `finance.forecasting.update`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Seed-from-actuals applies growth to trailing actuals correctly
- [ ] Three-way comparison numbers match GL + budget fixtures
- [ ] Scenario comparison renders all three when present
- [ ] Accuracy metric over closed-period fixtures

---

## Build Manifest

```
database/migrations/xxxx_create_fin_forecasts_table.php
database/migrations/xxxx_create_fin_forecast_lines_table.php
app/Models/Finance/{Forecast,ForecastLine}.php
app/Data/Finance/{CreateForecastData,ForecastComparisonData}.php
app/Services/Finance/ForecastService.php
app/Filament/Finance/Resources/ForecastResource.php
app/Filament/Finance/Pages/ForecastComparisonPage.php
database/factories/Finance/{ForecastFactory,ForecastLineFactory}.php
tests/Feature/Finance/ForecastTest.php
```

---

## Related

- [[domains/finance/budgets]]
- [[domains/finance/cash-flow]]
- [[domains/finance/general-ledger]]
