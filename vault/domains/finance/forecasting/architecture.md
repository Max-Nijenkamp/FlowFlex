---
domain: finance
module: forecasting
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Planning *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ForecastResource` | #1 CRUD resource | tweaks: custom-header-actions (seed-from-actuals), inline-relation-repeater (projected-line grid per account/period) | list filters: scenario, fiscal year; assumptions register (jsonb) editor |
| `ForecastComparisonPage` | #9 custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — scenarios side by side + three-way projected/actual/budget columns per account/period (apex charts); budget columns hidden when budgets inactive; realtime none | `/finance/forecasting/comparison` |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.forecasting.view-any') && BillingService::hasModule('finance.forecasting')`
per [[../../../architecture/filament-patterns]] #1. `ForecastComparisonPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Forecast + line CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Seed-from-actuals (trailing-actuals read → bulk-generate projected lines) | n-a | read-only ledger scan; generated lines are a derived/append bulk populate into own `fin_forecast_lines`, then refined under the Optimistic path above — the seed writes no ledger/source data |
| Three-way comparison (`comparison()`) | n-a | read-only computation over ledger + budget + forecast lines — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/patterns/custom-pages]], [[data-model]], [[api]].
