---
type: module
domain: Financial Planning & Analysis
panel: fpa
module-key: fpa.forecasting
status: planned
color: "#4ADE80"
---

# Financial Forecasting

> Rolling financial forecasts — revenue, costs, and headcount with monthly updates and scenario modelling.

**Panel:** `fpa`
**Module key:** `fpa.forecasting`

---

## What It Does

Financial Forecasting provides a rolling view of where the company is expected to finish the year based on actuals to date and updated forward projections. Finance teams update the forecast monthly — confirming actuals for the closed period and revising future-month estimates. Multiple scenarios (base, upside, downside) can be maintained simultaneously. The forecast is compared against the original budget to show the full-year variance expectation, and CRM pipeline data can be pulled in to inform the revenue forecast automatically.

---

## Features

### Core
- Rolling forecast creation: copy prior forecast; update actuals for closed periods; revise future months
- Revenue forecast: broken down by product line, customer segment, or geography
- Cost forecast: by department, cost centre, and GL category
- Full-year projection: actuals to date plus remaining forecast months
- Forecast vs budget: comparison view showing current forecast deviation from approved budget
- Monthly lock: lock each period as actuals are confirmed from the finance ledger

### Advanced
- Scenario modelling: maintain base, upside, and downside scenarios; switch between them with a click
- CRM pipeline integration: import probability-weighted pipeline from the CRM to inform the revenue forecast
- Driver-based forecasting: link revenue forecast lines to business drivers (e.g. new bookings × ASP, seats × price)
- Forecast accuracy tracking: compare prior forecasts against actual outcomes to measure FP&A accuracy
- Commentary per line: FP&A analysts add narrative explaining forecast changes

### AI-Powered
- Trend-based projection: AI suggests future-period forecast values based on historical trend and seasonality
- Revenue risk flagging: flag revenue forecast lines that are dependent on pipeline opportunities with low win probability
- Variance explanation: AI drafts a narrative explanation for significant forecast vs budget variances

---

## Data Model

```erDiagram
    forecast_versions {
        ulid id PK
        ulid company_id FK
        string name
        string scenario
        integer financial_year
        date as_of_date
        string status
        timestamps created_at_updated_at
    }

    forecast_lines {
        ulid id PK
        ulid version_id FK
        ulid company_id FK
        string line_type
        string department
        string gl_account
        json monthly_actuals
        json monthly_forecast
        decimal full_year_projection
        text commentary
        timestamps created_at_updated_at
    }

    forecast_versions ||--o{ forecast_lines : "contains"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `forecast_versions` | Forecast versions | `id`, `company_id`, `name`, `scenario`, `as_of_date`, `status` |
| `forecast_lines` | Line-level forecasts | `id`, `version_id`, `line_type`, `gl_account`, `monthly_actuals`, `monthly_forecast`, `full_year_projection` |

---

## Permissions

```
fpa.forecasting.view-any
fpa.forecasting.create
fpa.forecasting.update
fpa.forecasting.lock-periods
fpa.forecasting.export
```

---

## Filament

- **Resource:** `App\Filament\Fpa\Resources\ForecastVersionResource`
- **Pages:** `ListForecastVersions`, `CreateForecastVersion`, `ViewForecastVersion`
- **Custom pages:** `ForecastEntryPage`, `ScenarioComparisonPage`, `ForecastAccuracyPage`
- **Widgets:** `ForecastVsBudgetWidget`, `ForecastAccuracyWidget`
- **Nav group:** Forecasting

---

## Displaces

| Feature | FlowFlex | Anaplan | Adaptive Insights | Cube |
|---|---|---|---|---|
| Rolling forecast | Yes | Yes | Yes | Yes |
| Scenario modelling | Yes | Yes | Yes | Yes |
| CRM pipeline integration | Yes | Partial | No | No |
| AI trend projection | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[budget-planning]] — budget is the baseline that forecasts compare against
- [[variance-analysis]] — forecast feeds the expected full-year variance
- [[crm/INDEX]] — pipeline data imported to inform revenue forecast
- [[finance/INDEX]] — actuals data from finance ledger locks each month
