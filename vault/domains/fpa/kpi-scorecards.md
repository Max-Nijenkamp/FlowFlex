---
type: module
domain: Financial Planning & Analysis
panel: fpa
module-key: fpa.scorecards
status: planned
color: "#4ADE80"
---

# KPI Scorecards

> Executive KPI scorecards — financial and operational metrics with RAG status, trend charts, and configurable thresholds.

**Panel:** `fpa`
**Module key:** `fpa.scorecards`

---

## What It Does

KPI Scorecards delivers a curated, executive-level view of the business's most important financial and operational metrics in a single dashboard. Finance and FP&A leads configure which KPIs to track — revenue growth, gross margin, EBITDA, cash runway, NRR, headcount — set the target for each, and define the RAG thresholds. The scorecard automatically pulls current values from across the FlowFlex platform and presents them with trend sparklines and RAG indicators. It is designed to be the opening slide of a board pack or the CFO's weekly one-pager.

---

## Features

### Core
- KPI definition: name, category (financial, operational), data source, frequency (weekly, monthly, quarterly), and unit
- Target setting: annual target with RAG thresholds (green above X, amber between X and Y, red below Y)
- Automatic data pull: metrics sourced from finance ledger, billing MRR, HR headcount, CRM pipeline
- Scorecard view: grid of KPI tiles each showing current value, target, trend arrow, and RAG indicator
- Trend sparkline: 12-period trend chart per KPI
- PDF export: branded scorecard export for board pack inclusion

### Advanced
- Multiple scorecards: create separate scorecards for the board, CFO, and each department head
- Period comparison: show current period vs prior period and vs same period prior year
- Manual KPI entry: for metrics that cannot be auto-calculated, allow manual data entry
- Narrative block: add a free-text executive summary alongside the KPI tiles
- Scorecard scheduling: automatically email a PDF scorecard to configured recipients on the first of each month

### AI-Powered
- KPI commentary: AI drafts a brief narrative for each metric based on its value, trend, and prior period
- Target feasibility assessment: estimate whether each KPI target is achievable based on current trajectory
- Anomalous metric detection: flag KPIs that are moving in an unexpected direction relative to correlated metrics

---

## Data Model

```erDiagram
    kpi_definitions {
        ulid id PK
        ulid company_id FK
        string name
        string category
        string data_source
        string frequency
        string unit
        decimal target_value
        decimal green_threshold
        decimal amber_threshold
        boolean is_higher_better
        timestamps created_at_updated_at
    }

    scorecard_configs {
        ulid id PK
        ulid company_id FK
        string name
        string audience
        json kpi_ids
        boolean auto_email
        json email_recipients
        timestamps created_at_updated_at
    }

    kpi_snapshots {
        ulid id PK
        ulid kpi_id FK
        ulid company_id FK
        date snapshot_date
        decimal actual_value
        string rag_status
        timestamps created_at_updated_at
    }

    kpi_definitions ||--o{ kpi_snapshots : "tracked via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `kpi_definitions` | KPI configurations | `id`, `company_id`, `name`, `data_source`, `target_value`, `green_threshold`, `amber_threshold` |
| `scorecard_configs` | Scorecard layouts | `id`, `company_id`, `name`, `audience`, `kpi_ids`, `auto_email` |
| `kpi_snapshots` | Historical KPI values | `id`, `kpi_id`, `snapshot_date`, `actual_value`, `rag_status` |

---

## Permissions

```
fpa.scorecards.view
fpa.scorecards.manage-kpis
fpa.scorecards.manage-scorecards
fpa.scorecards.export
fpa.scorecards.view-all-scorecards
```

---

## Filament

- **Resource:** `App\Filament\Fpa\Resources\KpiDefinitionResource`
- **Pages:** `ListKpiDefinitions`, `CreateKpiDefinition`, `EditKpiDefinition`
- **Custom pages:** `ExecutiveScorecardPage`, `ScorecardConfigPage`, `KpiTrendPage`
- **Widgets:** `ExecutiveKpiGridWidget`, `RedMetricsWidget`
- **Nav group:** Analysis

---

## Displaces

| Feature | FlowFlex | Anaplan | Adaptive Insights | Power BI (embedded) |
|---|---|---|---|---|
| Configurable KPI scorecards | Yes | Yes | Yes | Custom |
| Auto data pull from platform | Yes | No | No | Custom |
| RAG thresholds | Yes | Yes | Yes | Custom |
| AI KPI commentary | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[variance-analysis]] — variance data feeds financial KPIs
- [[financial-forecasting]] — forecast vs target used in KPI RAG status
- [[subscription-billing/mrr-analytics]] — MRR and NRR pulled into financial scorecards
- [[hr/INDEX]] — headcount actuals pulled into operational KPIs
