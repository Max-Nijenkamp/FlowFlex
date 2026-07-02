---
domain: finance
module: forecasting
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Architecture

`ForecastService` owns forecast creation, seed-from-actuals, and the three-way comparison. The module owns forecast + line tables but reads actuals from the general ledger and budgeted figures from the budgets module — it produces projections, not source transactions.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw float math. Seed-from-actuals multiplies trailing actual cents by a growth factor with integer-cent rounding. See [[../../../architecture/packages]] (brick/money).

## Forecast models & seeding

- `seedFromActuals(forecastId, growthPercent)` copies trailing-12-month actuals per account/period from the ledger and applies the growth factor, producing projected line rows as a starting point.
- Manual editing of projected lines refines the seeded values.
- Driver-based forecasting models revenue from drivers (headcount, deals, units); driver values are manual, or pulled from crm.forecasting / hr.workforce when those soft-deps are present.

## Scenarios

Each forecast carries a `scenario` (base / optimistic / pessimistic). The comparison page renders scenarios side by side when multiple exist for the same fiscal year.

## Three-way comparison

`comparison(forecastId, period)` assembles, per account/period: projected (from forecast lines), actual (summed from journal lines), and budgeted (from budgets). Accuracy is tracked over closed periods via a MAPE-style metric *(assumed)*.

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/patterns/custom-pages]], [[data-model]], [[api]].
