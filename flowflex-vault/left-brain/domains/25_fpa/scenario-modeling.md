---
type: module
domain: Financial Planning & Analysis
panel: fpa
phase: 4
status: planned
cssclasses: domain-fpa
migration_range: 986500–986999
last_updated: 2026-05-09
---

# Scenario Modeling

Build and compare financial what-if models without touching live data. Answer "what happens to our P&L if we hire 10 engineers?" or "what if we drop pricing by 15%?" before committing.

---

## Model Types

### Headcount Scenarios
- Add/remove roles by department
- Loaded cost per hire (salary + employer NI + benefits + equipment)
- Effect on P&L: payroll cost, revenue per head (if hiring = more capacity)

### Pricing Scenarios
- Revenue impact of price change × current customer volume
- Churn sensitivity (higher price → assumed churn rate)
- Net revenue impact at different price elasticity assumptions

### Market/Growth Scenarios
- New market entry: extra revenue + extra cost (headcount, marketing, infra)
- Product line addition/removal
- M&A: add acquired entity's P&L to consolidated view

### Sensitivity Analysis
Single-variable sweep: vary one driver (e.g., gross margin from 60% to 80%) and see impact on EBITDA and cash. Outputs tornado chart.

---

## Model Canvas

Finance builds scenarios using a spreadsheet-style driver canvas:
- Rows: P&L line items
- Columns: months
- Cells: formula-driven (e.g., `=headcount[eng] × avg_loaded_cost[eng]`)
- Fork from current forecast or budget as starting point

---

## Comparison View

Side-by-side: up to 4 scenarios vs baseline.
- Waterfall chart: what changes between base and scenario
- IRR / payback period for investment scenarios

---

## Data Model

### `fpa_models`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| base_type | enum | budget/forecast/blank |
| base_id | ulid | nullable FK |
| created_by | ulid | FK |

### `fpa_model_lines`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| model_id | ulid | FK |
| gl_account_id | ulid | FK |
| period | date | |
| amount | decimal(14,2) | |
| formula | text | nullable |

---

## Migration

```
986500_create_fpa_models_table
986501_create_fpa_model_lines_table
986502_create_fpa_model_drivers_table
```

---

## Related

- [[MOC_FPA]]
- [[rolling-forecasts]]
- [[headcount-planning]]
- [[board-reporting-pack]]
