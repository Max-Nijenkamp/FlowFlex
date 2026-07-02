---
domain: finance
module: forecasting
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — DTOs, Services & Events

## DTOs

### CreateForecastData
| Field | Type | Validation |
|---|---|---|
| name | string | required |
| scenario | string | in:base,optimistic,pessimistic |
| fiscal_year | int | required |
| assumptions | array | `[{key, description, value}]` |
| lines | array | `[{account_id, period, projected_cents}]` |

### ForecastComparisonData (output)
- Per account/period: `projected_cents`, `actual_cents`, `budgeted_cents`, and derived variances.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

`ForecastService`:

- `create(CreateForecastData $data)` — persists forecast + projected lines.
- `seedFromActuals(string $forecastId, float $growthPercent): void` — copies trailing-12-month actuals × growth into projected lines (brick/money, integer cents).
- `comparison(string $forecastId, ?string $period = null): ForecastComparisonData` — three-way projected / actual / budget assembly.
- `accuracy(string $forecastId): float` — MAPE over closed periods *(assumed)*.

## Events

This module fires and consumes no cross-domain events. It reads actuals from the ledger and budget figures from the budgets module directly within the finance domain; driver inputs from crm.forecasting / hr.workforce are optional soft reads.

See [[security]], [[features/scenario-modelling]], [[features/seed-from-actuals]], [[../cash-flow/_module]].
