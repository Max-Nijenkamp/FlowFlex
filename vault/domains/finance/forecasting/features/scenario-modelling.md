---
domain: finance
module: forecasting
feature: scenario-modelling
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Scenario Modelling & Three-Way Comparison

Side-by-side base / optimistic / pessimistic forecasts, compared against actuals and budget.

- `ForecastResource` (#1 CRUD resource, [[../../../../architecture/ui-strategy]]): forecast header (name, scenario, fiscal year), assumptions register editor (jsonb), and the projected-line grid per account/period.
- `ForecastComparisonPage` (#9 report custom page + apex charts): renders each scenario for a fiscal year side by side, plus the three-way projected / actual / budget columns per account/period.
- `comparison(forecastId, period)` assembles projected (forecast lines), actual (summed journal lines), and budgeted (budget lines) as integer cents (brick/money).
- Variance columns are derived from the three sources; budget columns are hidden when the budgets module is inactive.

## UI
- **Kind**: simple-resource + custom-page (report)
- **Page**: `ForecastResource` under `/finance/forecasting`; `ForecastComparisonPage` under `/finance/forecasting/comparison`.
- **Layout**: resource = forecast header (name, scenario, fiscal year) + assumptions register editor (jsonb) + projected-line grid per account/period. Comparison page = apex charts + three-way projected/actual/budget columns side by side per scenario.
- **Key interactions**: edit assumptions + projected lines, switch scenario, compare projected vs actual vs budget.
- **States**: empty (no forecast → create or seed) · loading (grid/chart skeleton) · error (validation on lines/assumptions) · selected (active scenario; budget columns hidden when budgets module inactive)
- **Gating**: `finance.forecasting.manage` (create/edit), `finance.forecasting.view-any` (comparison)

## Data
- Owns / writes: `fin_forecasts`, `fin_forecast_lines` only. All amounts integer minor units via brick/money.
- Reads (all read-only): actuals = journal lines from finance.ledger; budget lines from finance.budgets (comparison, hidden when inactive).
- Cross-domain writes: none. Never writes ledger or budgets tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no events.
- Feeds: no events. In-domain service calls (`comparison`) and read calls into ledger/budgets; see [[seed-from-actuals]].

See [[../architecture]], [[../api]], [[../data-model]], [[seed-from-actuals]].
