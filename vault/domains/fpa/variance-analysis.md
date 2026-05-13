---
type: module
domain: Financial Planning & Analysis
panel: fpa
module-key: fpa.variance
status: planned
color: "#4ADE80"
---

# Variance Analysis

> Budget vs actual variance analysis with drill-down by entity, period, and category — and forecast vs budget deviation.

**Panel:** `fpa`
**Module key:** `fpa.variance`

---

## What It Does

Variance Analysis is the primary analytical tool for finance controllers and FP&A managers. It shows, for any period, the difference between actual results (from the finance ledger) and the approved budget, and the difference between the current forecast and the original budget. Users can drill down from company-wide totals to department, cost centre, and individual GL account level. Variance explanations can be added per line item, and the analysis can be exported for board and management reporting packs.

---

## Features

### Core
- Budget vs actual: total variance in absolute and percentage terms by period and year-to-date
- Forecast vs budget: full-year expected variance based on current forecast
- Drill-down: navigate from company → entity → department → cost centre → GL account
- Favourable/unfavourable flagging: colour-coded variance with F/U indicators
- Period comparison: select any month, quarter, or year-to-date period
- Export: export variance report to CSV or Excel for inclusion in management packs

### Advanced
- Commentary per line: finance adds explanatory notes to material variances
- Materiality threshold: filter to show only variances above a configurable percentage or absolute threshold
- Rolling 12-month variance: view the variance trend over the trailing twelve months
- Multi-entity consolidation: consolidate variance across legal entities for group reporting
- Custom groupings: group GL accounts into management reporting categories different from the chart of accounts

### AI-Powered
- Root cause analysis: AI suggests the most likely cause of a variance based on correlated data points
- Variance narrative: draft the management commentary section for a board report from structured variance data
- Recurring variance pattern: flag GL accounts that consistently show a variance in the same direction across multiple periods

---

## Data Model

```erDiagram
    variance_snapshots {
        ulid id PK
        ulid company_id FK
        string period_type
        date period_date
        string department
        string cost_centre
        string gl_account
        decimal budget_amount
        decimal actual_amount
        decimal forecast_amount
        decimal bva_variance
        decimal fvb_variance
        decimal bva_variance_percent
        text commentary
        timestamps created_at_updated_at
    }
```

| Table | Purpose | Key Columns |
|---|---|---|
| `variance_snapshots` | Pre-aggregated variance data | `id`, `company_id`, `period_date`, `department`, `gl_account`, `budget_amount`, `actual_amount`, `bva_variance`, `bva_variance_percent` |

Note: Variance data is computed from budget lines, forecast lines, and finance ledger actuals via scheduled aggregation.

---

## Permissions

```
fpa.variance.view-own-department
fpa.variance.view-all
fpa.variance.add-commentary
fpa.variance.export
fpa.variance.view-entity-consolidation
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `VarianceDashboardPage`, `DrillDownPage`, `EntityConsolidationPage`
- **Widgets:** `BvaVarianceWidget`, `TopVarianceLinesWidget`, `YtdSummaryWidget`
- **Nav group:** Analysis

---

## Displaces

| Feature | FlowFlex | Anaplan | Adaptive Insights | Cube |
|---|---|---|---|---|
| Budget vs actual drill-down | Yes | Yes | Yes | Yes |
| Forecast vs budget | Yes | Yes | Yes | Yes |
| AI root cause analysis | Yes | No | No | No |
| AI narrative drafting | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[budget-planning]] — approved budget is one input to variance
- [[financial-forecasting]] — forecast provides the FvB variance
- [[finance/INDEX]] — actuals from the finance ledger are the other input
- [[kpi-scorecards]] — variance data feeds KPI scorecards
