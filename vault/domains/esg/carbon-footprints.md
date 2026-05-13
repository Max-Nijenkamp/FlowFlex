---
type: module
domain: ESG & Sustainability
panel: esg
module-key: esg.carbon
status: planned
color: "#4ADE80"
---

# Carbon Footprints

> Scope 1, 2, and 3 greenhouse gas emissions tracking with calculation methodology selection, target setting, and progress monitoring.

**Panel:** `esg`
**Module key:** `esg.carbon`

---

## What It Does

Carbon Footprints provides the quantitative backbone of the ESG panel by tracking the company's greenhouse gas emissions across all three scopes. Emissions data is entered from multiple sources — energy bills, fuel consumption, business travel, supply chain estimates — and the system applies the correct emission factors based on the selected methodology (GHG Protocol, DEFRA, or custom factors). Net emission totals are tracked against annual reduction targets, and the data feeds directly into ESG report generation.

---

## Features

### Core
- Emissions categories: Scope 1 (direct combustion), Scope 2 (purchased electricity/heat), Scope 3 (supply chain, travel, waste, commuting)
- Data entry: enter activity data (kWh, litres of fuel, km travelled) or direct tonne CO₂e values
- Emission factor library: built-in GHG Protocol and DEFRA emission factors by category and geography
- Calculation engine: automatically convert activity data to tonne CO₂e using selected factors
- Annual reduction targets: set target tonne CO₂e or percentage reduction targets per scope
- Progress tracking: monthly progress toward annual target with projected year-end total

### Advanced
- Custom emission factors: override built-in factors for company-specific data sources
- Data source integration: import energy meter data or utility bill totals from uploaded CSV
- Baseline year: set a baseline year against which reduction progress is measured
- Offset tracking: log carbon offset purchases and net-out from gross emissions
- Data quality flags: mark data entries as estimated vs measured for reporting transparency

### AI-Powered
- Anomaly detection: flag emission data entries that are implausibly high or low for the category
- Reduction opportunity suggestions: identify the highest-impact emission categories for reduction action
- Forecast to target: project whether the company will hit its annual target based on current trajectory

---

## Data Model

```erDiagram
    carbon_emission_records {
        ulid id PK
        ulid company_id FK
        integer scope
        string category
        string sub_category
        decimal activity_value
        string activity_unit
        decimal emission_factor
        string factor_source
        decimal tonne_co2e
        string data_quality
        date period_from
        date period_to
        timestamps created_at_updated_at
    }

    carbon_targets {
        ulid id PK
        ulid company_id FK
        integer target_year
        integer baseline_year
        decimal baseline_tonne_co2e
        decimal target_tonne_co2e
        decimal reduction_percent
        timestamps created_at_updated_at
    }

    carbon_emission_records }o--|| companies : "belongs to"
    carbon_targets }o--|| companies : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `carbon_emission_records` | Emission data entries | `id`, `company_id`, `scope`, `category`, `tonne_co2e`, `data_quality`, `period_from` |
| `carbon_targets` | Reduction targets | `id`, `company_id`, `target_year`, `baseline_tonne_co2e`, `target_tonne_co2e` |

---

## Permissions

```
esg.carbon.view
esg.carbon.enter-data
esg.carbon.manage-factors
esg.carbon.set-targets
esg.carbon.export
```

---

## Filament

- **Resource:** `App\Filament\Esg\Resources\CarbonEmissionResource`
- **Pages:** `ListCarbonEmissions`, `CreateCarbonEmission`, `EditCarbonEmission`
- **Custom pages:** `CarbonDashboardPage`, `ScopeSummaryPage`, `TargetProgressPage`
- **Widgets:** `TotalEmissionsWidget`, `ScopeBreakdownWidget`, `TargetProgressWidget`
- **Nav group:** Environment

---

## Displaces

| Feature | FlowFlex | Watershed | Plan A | Normative |
|---|---|---|---|---|
| Scope 1/2/3 tracking | Yes | Yes | Yes | Yes |
| GHG Protocol factors | Yes | Yes | Yes | Yes |
| Target tracking | Yes | Yes | Yes | Yes |
| AI reduction suggestions | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[esg-kpis]] — carbon intensity is tracked as an ESG KPI
- [[esg-reports]] — emission data feeds GRI and CSRD reports
- [[supply-chain]] — Scope 3 supply chain emissions tracked via supplier assessments
- [[travel/bookings]] — business travel distances feed Scope 3 calculation
