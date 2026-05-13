---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.forecasting
status: planned
color: "#4ADE80"
---

# Forecasting

> Sales forecasts by period, rep, and territory — weighted pipeline, quota tracking, and commit/best-case/most-likely scenario breakdowns.

**Panel:** `crm`
**Module key:** `crm.forecasting`

## What It Does

Forecasting gives sales leadership a structured view of expected revenue for the current and upcoming periods. The weighted pipeline (deal value × probability) is the base layer. Reps submit their commit (deals they are certain to close) and best-case (deals that could close with some effort) forecasts. Managers review and adjust forecasts at the rep level. The forecast roll-up compares committed pipeline and weighted pipeline against quota to give a clear picture of whether the team will hit their number. Forecasts are snapshotted weekly so trend lines can be drawn.

## Features

### Core
- Weighted pipeline: all open deals summed as value × probability per stage — per rep, per territory, per period
- Quota tracking: quota set per rep per period — attainment % shown as current won deals ÷ quota
- Commit forecast: rep manually flags which deals they are committing to close in the period — separate from weighted pipeline
- Best-case forecast: rep flags additional deals that could close under favourable conditions
- Period selector: monthly, quarterly, or custom period — forecast recalculates for the selected window

### Advanced
- Forecast roll-up: rep-level forecasts aggregated to team → region → company level — manager can override individual rep forecasts
- Forecast snapshot: weekly automated snapshot of weighted pipeline, commit, and won values — trend lines drawn across snapshots
- Gap analysis: if commit + best-case does not cover quota, a gap is highlighted with the shortfall amount
- Pipeline coverage ratio: total open pipeline ÷ remaining quota — industry benchmark is 3× coverage; flagged if below 2×
- Historical accuracy: compare prior period commit forecast to actual won revenue — per rep accuracy score

### AI-Powered
- AI forecast: parallel to the rep's manual forecast, AI generates a forecast based on deal health scores, stage velocity, and historical win rates — shown alongside rep forecast for comparison
- Deal push detection: AI identifies deals that have been pushed to the next period multiple times — flags them as likely to push again or be lost

## Data Model

```erDiagram
    sales_quotas {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string period
        decimal quota_amount
        timestamps created_at/updated_at
    }

    forecast_snapshots {
        ulid id PK
        ulid company_id FK
        string period
        ulid user_id FK
        decimal weighted_pipeline
        decimal commit_amount
        decimal best_case_amount
        decimal won_amount
        timestamp snapshot_date
        timestamps created_at/updated_at
    }

    deal_forecast_categories {
        ulid deal_id FK
        string category
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `category` | commit / best_case / pipeline / omitted |
| `weighted_pipeline` | Sum of (value × probability) for open deals |
| `snapshot_date` | Weekly snapshot timestamp |

## Permissions

- `crm.forecasting.view-own`
- `crm.forecasting.view-team`
- `crm.forecasting.submit-forecast`
- `crm.forecasting.override-rep-forecast`
- `crm.forecasting.manage-quotas`

## Filament

- **Resource:** `SalesQuotaResource`
- **Pages:** `ListSalesQuotas`
- **Custom pages:** `ForecastDashboardPage` — period selector, quota attainment gauge, commit vs weighted vs won waterfall
- **Widgets:** `QuotaAttainmentWidget` — team quota attainment % on CRM dashboard
- **Nav group:** Pipeline (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Salesforce Forecasting | Sales forecast management |
| Clari | Revenue forecasting and pipeline inspection |
| BoostUp | AI-driven sales forecasting |
| HubSpot Forecast | CRM-native sales forecast |

## Related

- [[deals]]
- [[pipeline]]
- [[territory-management]]
- [[revenue-intelligence]]
