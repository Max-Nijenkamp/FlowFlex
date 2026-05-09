---
type: moc
domain: Financial Planning & Analysis
panel: fpa
phase: 4
color: "#6366F1"
cssclasses: domain-fpa
last_updated: 2026-05-09
---

# Financial Planning & Analysis (FP&A) — Map of Content

Forward-looking financial management: annual budgeting, rolling forecasts, budget vs actual reporting, headcount planning, scenario modeling, and board reporting packs. Replaces Anaplan, Planful, Mosaic, Cube, and Pigment for mid-market companies.

**Panel:** `fpa`  
**Phase:** 4  
**Migration Range:** `985000–989999`  
**Colour:** Indigo `#6366F1` / Light: `#EEF2FF`  
**Icon:** `heroicon-o-chart-bar`

---

## Why This Domain Exists

Every company above 20 people needs to plan and measure financial performance. Finance GL tracks what happened — FP&A plans what should happen and measures the gap.

Current tools are expensive:
- Anaplan: €50k+/year
- Planful: €30k+/year
- Mosaic / Cube: €2k–5k/month
- Even Excel is painful and error-prone at scale

FlowFlex Finance has the actuals (GL). FP&A adds the planning layer on top.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Annual Budget Builder | 4 | planned | Department budgets, headcount plans, assumptions |
| Budget vs Actual Reporting | 4 | planned | Monthly variance analysis, auto-commentary |
| Rolling Forecasts | 4 | planned | Update forecast monthly with actuals; 12-month rolling view |
| Scenario Modeling | 5 | planned | What-if: change revenue growth rate, headcount — instant P&L impact |
| Headcount Planning | 4 | planned | Approved headcount per department, planned hire dates, cost |
| Board Reporting Pack | 5 | planned | Auto-generate monthly board pack: P&L, cash, KPIs, commentary |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `BudgetApproved` | Budget Builder | Finance (set period budgets), Notifications (dept heads) |
| `VarianceThresholdBreached` | Budget vs Actual | Notifications (CFO, dept head), FP&A (flag for commentary) |
| `ForecastUpdated` | Rolling Forecasts | Notifications (management team), Analytics |
| `BoardPackGenerated` | Board Pack | Notifications (board members) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Planning` — Budget Builder, Headcount Plan, Assumptions
- `Forecasting` — Rolling Forecast, Monthly Update, Forecast History
- `Reporting` — Budget vs Actual, Variance Analysis, Trend Reports
- `Scenarios` — Scenario Library, What-If Modeler, Sensitivity Analysis
- `Board` — Board Pack Generator, KPI Dashboard, Investor Metrics

---

## Data Sources

FP&A reads from:
- Finance GL → actuals (revenue, costs, cash)
- HR → headcount actuals, approved positions, salaries
- Subscriptions → MRR/ARR actuals
- Projects → billable revenue actuals
- Procurement → committed spend

---

## Permissions Prefix

`fpa.budgets.*` · `fpa.forecasts.*` · `fpa.reporting.*`  
`fpa.scenarios.*` · `fpa.board.*`

---

## Competitors Displaced

Anaplan · Planful · Mosaic · Cube · Causal · Pigment · Adaptive Insights (Workday)

---

## Related

- [[MOC_Domains]]
- [[MOC_Finance]] — GL actuals feed FP&A
- [[MOC_HR]] — headcount data
- [[MOC_SubscriptionBilling]] — ARR/MRR metrics
- [[MOC_Analytics]] — board pack analytics
