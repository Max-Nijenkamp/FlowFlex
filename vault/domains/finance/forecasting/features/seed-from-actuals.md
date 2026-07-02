---
domain: finance
module: forecasting
feature: seed-from-actuals
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Seed From Actuals

Bootstrap a forecast from trailing actuals plus a growth assumption, then refine.

- `ForecastService::seedFromActuals(forecastId, growthPercent)` reads the trailing-12-month actuals per account/period from the general ledger and writes projected lines scaled by the growth factor.
- Arithmetic is integer-cent (brick/money): `projected = round(actual_cents × (1 + growthPercent/100))` — no float accumulation.
- Seeded lines are a starting point: they are freely editable afterwards and can be overridden by driver-based refinements.
- Accuracy tracking later compares these projected lines against realised actuals per closed period (MAPE-style metric *(assumed)*).

## UI
- **Kind**: background (triggered by an action button on `ForecastResource`)
- **Page**: "Seed from actuals" action on `ForecastResource` under `/finance/forecasting/{id}`.
- **Layout**: action opens a small form (growth %); on run, populates the projected-line grid.
- **Key interactions**: trigger the action, enter growth %, run `ForecastService::seedFromActuals`, then refine lines afterwards.
- **States**: empty (no trailing actuals to seed from) · loading (seeding) · error (validation on growth %) · selected (seeded lines populated, freely editable)
- **Gating**: `finance.forecasting.manage`

## Data
- Owns / writes: `fin_forecast_lines` (writes projected lines, integer-cent arithmetic via brick/money: `projected = round(actual_cents × (1 + growth/100))`, no float accumulation).
- Reads: trailing-12-month actuals = journal lines from finance.ledger, read-only.
- Cross-domain writes: none — writes only own forecast lines. Never writes ledger tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no events.
- Feeds: no events. In-domain service call `seedFromActuals`; cross-domain read of ledger actuals. Accuracy MAPE tracking *(assumed)*.

See [[../architecture]], [[../api]], [[scenario-modelling]], [[../../general-ledger/_module]].
