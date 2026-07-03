---
domain: finance
module: forecasting
type: module
module-key: finance.forecasting
priority: v1
build-status: planned
status: wip
depends-on: [finance.ledger, finance.budgets, core.billing, core.rbac]
soft-depends: [crm.forecasting, hr.workforce, finance.cashflow]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: [fin_forecasts, fin_forecast_lines]
permission-prefix: finance.forecasting
encrypted-fields: []
color: "#4ADE80"
updated: 2026-07-03
---

# Forecasting

Financial forecasting, scenario modelling, and variance analysis. Absorbed from the former FP&A domain. Rolling forecasts project revenue/expense per period from historical actuals plus growth assumptions, compared three ways against actuals and budget.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Module-key

`finance.forecasting`

**Priority:** v1  
**Panel:** finance  
**Permission prefix:** `finance.forecasting`  
**Tables:** `fin_forecasts`, `fin_forecast_lines`

## Purpose

The module produces rolling forecasts: projected revenue/expense per period, updated monthly, across base / optimistic / pessimistic scenarios. Forecasts can be driver-based (revenue modelled from headcount, deals, units) with driver values entered manually or pulled from soft-dependency modules. Each forecast keeps a documented assumptions register and its accuracy is tracked over closed periods.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | historical actuals base for seed-from-actuals + comparison |
| Hard | [[../budgets/_module\|finance.budgets]] | three-way (forecast vs actual vs budget) comparison |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../crm/forecasting/_module\|crm.forecasting]], [[../../hr/workforce-planning/_module\|hr.workforce]] | driver inputs (pipeline, headcount); manual drivers without them |
| Soft | [[../cash-flow/_module\|finance.cashflow]] | cash position projection link |

## Core Features

- Rolling forecast: projected revenue/expense per period, updated monthly.
- Forecast models: based on historical actuals + growth assumptions.
- Scenario comparison: base / optimistic / pessimistic side by side.
- Driver-based forecasting: model revenue from drivers (headcount, deals, units) — driver values manual or pulled from soft-dep modules.
- Forecast vs actual vs budget three-way comparison.
- Assumptions register: documented assumptions per forecast (jsonb).
- Forecast accuracy tracking over time (projected vs realised per closed period).
- Cash position projection (links Cash Flow).

## Permissions

`finance.forecasting.view-any` · `finance.forecasting.create` · `finance.forecasting.update`

## Test Checklist

- [ ] Tenant isolation: company A cannot see or edit company B forecasts/lines
- [ ] Module gating: artifacts hidden when `finance.forecasting` inactive
- [ ] Seed-from-actuals applies growth to trailing actuals correctly (brick/money)
- [ ] Three-way comparison numbers match GL + budget fixtures
- [ ] Scenario comparison renders all three when present
- [ ] Accuracy metric over closed-period fixtures

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

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_forecasts`, `fin_forecast_lines`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Reads | `fin_journal_lines` (read-only) for trailing actuals | [[../general-ledger/_module\|finance.ledger]] |
| Reads | `fin_budget_lines` (read-only) for comparison | [[../budgets/_module\|finance.budgets]] |

## Entity Notes

- [[architecture]] — forecast models, seed-from-actuals, scenarios, three-way comparison
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, permissions
- [[decisions]] — scenario model, accuracy metric
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/scenario-modelling]], [[features/seed-from-actuals]]

## Related

- [[../budgets/_module]]
- [[../cash-flow/_module]]
- [[../general-ledger/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
