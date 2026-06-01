---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.forecasting
status: planned
color: "#4ADE80"
---

# Forecasting

Financial forecasting, scenario modelling, and variance analysis. Absorbed from the former FP&A domain.

## Core Features

- Rolling forecast: projected revenue/expense per period, updated monthly
- Forecast models: based on historical actuals + growth assumptions
- Scenario comparison: base / optimistic / pessimistic side by side
- Driver-based forecasting: model revenue from drivers (headcount, deals, units)
- Forecast vs actual vs budget three-way comparison
- Assumptions register: documented assumptions per forecast
- Forecast accuracy tracking over time
- Cash position projection (links Cash Flow)

## Data Model

| Table | Key Columns |
|---|---|
| `fin_forecasts` | company_id, name, scenario (base/optimistic/pessimistic), fiscal_year, assumptions (json) |
| `fin_forecast_lines` | forecast_id, company_id, account_id, period, projected_cents |

## Filament

**Nav group:** Planning

- `ForecastResource` — build forecasts, set scenarios + assumptions
- `ForecastComparisonPage` (custom page) — forecast vs actual vs budget charts

## Cross-Domain

- Actuals from General Ledger; targets from Budgets
- Drivers from CRM (pipeline), HR (headcount plan)

## Related

- [[domains/finance/budgets]]
- [[domains/finance/cash-flow]]
- [[domains/finance/general-ledger]]
