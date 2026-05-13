---
type: module
domain: ESG & Sustainability
panel: esg
module-key: esg.kpis
status: planned
color: "#4ADE80"
---

# ESG KPIs

> ESG KPI library with custom metric definitions, target setting, actuals entry, and trend tracking across environmental, social, and governance dimensions.

**Panel:** `esg`
**Module key:** `esg.kpis`

---

## What It Does

ESG KPIs provides a structured framework for tracking all quantitative ESG metrics beyond carbon emissions — renewable energy percentage, female leadership representation, employee volunteering hours, supplier diversity spend, data breach count, board independence ratio, and hundreds of other metrics that feature in ESG frameworks. Sustainability managers define the KPI library, set annual targets, and enter actuals on a regular cadence. Trend charts show whether the organisation is on track across all three ESG pillars.

---

## Features

### Core
- KPI library: pre-built metrics mapped to GRI, CSRD, TCFD, and UN SDG frameworks, plus custom metric creation
- Metric dimensions: environmental, social, and governance categories with subcategories
- Target setting: annual or multi-year targets with a unit of measurement (percentage, number, hours, tonnes)
- Actuals entry: quarterly or monthly data entry with notes and data source references
- Trend view: rolling 4-quarter trend line for each KPI with target line overlay
- RAG status: automatic red/amber/green status based on progress toward target

### Advanced
- Benchmark comparisons: compare KPIs against industry sector benchmarks where available
- Weighted composite score: create a composite ESG score by weighting selected KPIs
- Data owner assignment: assign a specific team member responsible for each KPI data entry
- Historical data import: bulk import historical KPI actuals from a CSV
- KPI dependencies: link a KPI to a sustainability initiative to track initiative impact on the metric

### AI-Powered
- Anomalous data detection: flag KPI entries that are implausible given historical patterns
- KPI recommendation: suggest additional KPIs based on the company's industry and active ESG frameworks
- Target achievability assessment: estimate the likelihood of hitting the annual target based on trajectory

---

## Data Model

```erDiagram
    esg_kpis {
        ulid id PK
        ulid company_id FK
        string name
        string dimension
        string category
        string unit
        json framework_mappings
        ulid data_owner_id FK
        boolean is_active
        timestamps created_at_updated_at
    }

    esg_kpi_targets {
        ulid id PK
        ulid kpi_id FK
        integer target_year
        decimal target_value
        timestamps created_at_updated_at
    }

    esg_kpi_actuals {
        ulid id PK
        ulid kpi_id FK
        string period_type
        date period_date
        decimal actual_value
        text notes
        string data_source
        timestamps created_at_updated_at
    }

    esg_kpis ||--o{ esg_kpi_targets : "has"
    esg_kpis ||--o{ esg_kpi_actuals : "tracked via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `esg_kpis` | KPI definitions | `id`, `company_id`, `name`, `dimension`, `unit`, `framework_mappings` |
| `esg_kpi_targets` | Annual targets | `id`, `kpi_id`, `target_year`, `target_value` |
| `esg_kpi_actuals` | Entered actuals | `id`, `kpi_id`, `period_date`, `actual_value`, `data_source` |

---

## Permissions

```
esg.kpis.view
esg.kpis.manage-library
esg.kpis.enter-actuals
esg.kpis.set-targets
esg.kpis.export
```

---

## Filament

- **Resource:** `App\Filament\Esg\Resources\EsgKpiResource`
- **Pages:** `ListEsgKpis`, `CreateEsgKpi`, `EditEsgKpi`, `ViewEsgKpi`
- **Custom pages:** `KpiDashboardPage`, `FrameworkMappingPage`
- **Widgets:** `EsgScoreWidget`, `OffTrackKpisWidget`, `TrendSummaryWidget`
- **Nav group:** Environment

---

## Displaces

| Feature | FlowFlex | Watershed | Plan A | Normative |
|---|---|---|---|---|
| Custom KPI library | Yes | Partial | Yes | No |
| Framework mapping | Yes | Partial | Yes | Yes |
| Trend tracking | Yes | Yes | Yes | No |
| AI KPI recommendations | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[carbon-footprints]] — carbon intensity KPIs use emission data
- [[sustainability-initiatives]] — initiatives linked to KPIs they aim to improve
- [[esg-reports]] — KPI actuals feed framework report sections
