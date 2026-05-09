---
type: module
domain: Financial Planning & Analysis
panel: fpa
phase: 4
status: planned
cssclasses: domain-fpa
migration_range: 986000–986499
last_updated: 2026-05-09
---

# Rolling Forecasts

Replace static annual budget with a live 12- or 18-month forward view that updates every month. Management always has a current view of where the year will land.

---

## Rolling vs Static Budget

| | Static Budget | Rolling Forecast |
|---|---|---|
| Frequency | Set once per year | Updated monthly |
| Horizon | 12 months from Jan | Always 12–18 months ahead |
| Value | Year-end target | Current best estimate |
| Stale? | Yes by Q3 | Never |

Both coexist: budget is the target, rolling forecast is the best estimate of outcome.

---

## Forecast Drivers

Finance team configures driver-based assumptions:
- **Revenue**: pipeline-weighted, subscription ARR growth rate, seasonal factors
- **Headcount costs**: current headcount × rates + planned hires
- **COGS**: revenue × gross margin %
- **OpEx**: actuals trend + known step changes (new software contract in Oct)
- **CapEx**: project-by-project input

Assumptions documented and versioned — every forecast run has a record of what was assumed.

---

## Forecast Cadence

1. **Monthly update**: auto-populate actuals for closed months, re-run forecast for open months
2. **Department input**: managers confirm/adjust their forecast for next quarter
3. **Finance consolidation**: merge department forecasts with top-level assumptions
4. **Published forecast**: locked snapshot for reporting

---

## Scenarios Within Forecast

Each forecast can carry multiple scenarios:
- **Base**: most likely
- **Upside**: if top 3 deals close
- **Downside**: if churn accelerates

Scenario comparison view shows P&L under each case.

---

## Data Model

### `fpa_forecast_runs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(100) | "May 2026 Forecast" |
| horizon_months | tinyint | 12 or 18 |
| status | enum | draft/submitted/approved |
| forecast_date | date | |

### `fpa_forecast_lines`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| run_id | ulid | FK |
| cost_centre_id | ulid | FK |
| gl_account_id | ulid | FK |
| scenario | enum | base/upside/downside |
| period | date | month |
| amount | decimal(14,2) | |
| is_actual | boolean | true for closed months |

---

## Migration

```
986000_create_fpa_forecast_runs_table
986001_create_fpa_forecast_lines_table
986002_create_fpa_forecast_assumptions_table
```

---

## Related

- [[MOC_FPA]]
- [[annual-budget-builder]]
- [[scenario-modeling]]
- [[budget-vs-actual-reporting]]
- [[board-reporting-pack]]
