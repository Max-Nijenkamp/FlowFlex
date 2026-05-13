---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.reports
status: planned
color: "#4ADE80"
---

# Reports

> Tabular data extractions from any FlowFlex domain, available on-demand or on a schedule, with CSV and PDF export.

**Panel:** `analytics`
**Module key:** `analytics.reports`

## What It Does

The Reports module handles structured tabular data extraction across all FlowFlex domains. Unlike Dashboards (ongoing visual monitoring), this module targets point-in-time data extractions: monthly P&L, payroll summary, stock valuation, pipeline by rep. A pre-built template library gives every team immediate value. Teams can also build custom extractions by selecting data sources, columns, filters, and groupings, then preview and export to CSV or PDF.

## Features

### Core
- Template library: 50+ pre-built templates across Finance (P&L, AR aging, expense by category), HR (headcount, leave balances, payroll summary), CRM (pipeline by stage, won deals this month), Operations (stock valuation, PO summary)
- Custom builder: pick data source, choose columns, set filters, apply grouping and sorting
- On-demand execution: generate immediately with in-browser preview before downloading
- Export formats: CSV for raw data, PDF formatted with company logo and title header
- Parameters: date range picker, entity filter (department, team, product line), and per-template custom inputs
- Execution history: log of every run with timestamp, user, parameters used, row count, and download link

### Advanced
- Saved configurations: give a custom setup a name and re-run it with one click
- Folders: organise saved configurations by domain or team
- Column formatting: currency symbol, percentage, date format, and conditional cell colour (red for negative values)
- Cross-domain joins: combine data from multiple modules in one table (headcount by department alongside payroll cost)
- Pivot mode: rotate dimensions onto rows and columns for cross-tab analysis
- Bundles: package multiple outputs into a single PDF booklet for board packs

### AI-Powered
- Narrative summary: auto-generate a plain-language paragraph highlighting the most important findings
- Anomaly callouts: automatically flag rows that deviate significantly from the prior period average

## Data Model

```erDiagram
    an_report_templates {
        ulid id PK
        ulid company_id FK
        string name
        string domain
        string data_source_key
        json column_config
        json default_filters
        boolean is_system_template
        timestamps timestamps
    }

    an_saved_reports {
        ulid id PK
        ulid company_id FK
        ulid template_id FK
        string name
        string folder
        ulid created_by FK
        json parameters
        timestamps timestamps
    }

    an_report_runs {
        ulid id PK
        ulid saved_report_id FK
        ulid run_by FK
        json parameters_used
        string status
        string export_format
        string file_url
        integer row_count
        timestamp ran_at
    }

    an_report_templates ||--o{ an_saved_reports : "used in"
    an_saved_reports ||--o{ an_report_runs : "generates"
```

| Table | Purpose |
|---|---|
| `an_report_templates` | System and custom template definitions |
| `an_saved_reports` | User-saved configurations |
| `an_report_runs` | Execution history with export file links |

## Permissions

```
analytics.reports.view-any
analytics.reports.run
analytics.reports.create
analytics.reports.export
analytics.reports.manage-templates
```

## Filament

**Resource class:** `DataReportResource`
**Pages:** List, View
**Custom pages:** `ReportBuilderPage` (column and filter configuration), `ReportRunPage` (in-browser preview with export button)
**Widgets:** `RecentRunsWidget` (last 5 executions by the current user)
**Nav group:** Reports

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Crystal Reports | Structured tabular output with PDF export |
| Power BI Paginated | Template-based data extraction |
| Sage built-in reporting | Domain-specific pre-built template library |
| Tableau data extracts | Custom data export with filtering |

## Related

- [[dashboards]] — visual monitoring layer; this module handles tabular extractions
- [[scheduled-reports]] — saved configurations can be delivered on a schedule
- [[kpi-metrics]] — KPI actuals sourced from extracted data
- [[data-connectors]] — external data sources available in the custom builder
