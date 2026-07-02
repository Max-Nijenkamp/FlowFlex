---
domain: finance
module: forecasting
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Decisions

## Scenarios as separate forecast rows

Base / optimistic / pessimistic are modelled as distinct `fin_forecasts` rows sharing a name + fiscal year, not as columns on one forecast. This keeps line editing simple and lets the comparison page render whichever scenarios exist side by side.

## Seed-from-actuals as a starting point, not a lock

`seedFromActuals` populates projected lines from trailing-12-month actuals × growth, then lines are freely editable. The seed is a convenience, not a binding model — driver-based refinements override it.

## Accuracy via MAPE over closed periods

Forecast accuracy is tracked with a MAPE-style metric comparing projected vs realised per closed period *(assumed)*. Overridable via ADR if a different error metric is preferred.

## Advisory, non-posting

Forecasts do not post to the general ledger; they are planning artifacts only. No approval workflow or immutability is applied.

See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]], [[unknowns]].
