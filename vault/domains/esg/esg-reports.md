---
type: module
domain: ESG & Sustainability
panel: esg
module-key: esg.reports
status: planned
color: "#4ADE80"
---

# ESG Reports

> Regulatory ESG report generation aligned to GRI, CSRD, and TCFD frameworks — data aggregation, narrative, and PDF export.

**Panel:** `esg`
**Module key:** `esg.reports`

---

## What It Does

ESG Reports provides the compliance reporting output for the ESG panel. Sustainability managers select a reporting framework (GRI Standards, CSRD, TCFD, or a custom structure), and the system automatically maps relevant KPI actuals and carbon data to the required disclosure sections. Managers complete any remaining narrative fields, review the assembled report, and export it as a formatted PDF. Report versions are stored for audit, and the same underlying data can generate multiple framework outputs simultaneously.

---

## Features

### Core
- Framework selection: GRI, CSRD, TCFD, or custom disclosure structure
- Automatic data mapping: KPI actuals and carbon data pre-populate the relevant disclosure fields
- Narrative sections: rich text fields for qualitative disclosures alongside quantitative data
- Report preview: in-browser preview of the assembled report before export
- PDF export: branded, formatted PDF suitable for publication or regulatory submission
- Report versioning: store multiple versions of a report with publication status

### Advanced
- Multi-framework reporting: generate GRI and CSRD reports from the same underlying data in one cycle
- Materiality assessment integration: link to a materiality matrix to demonstrate which topics were included and why
- Assurance-ready data export: export the underlying data table with source references for third-party assurance
- Regulatory deadline tracking: set deadlines for each framework submission and receive reminders
- Disclosure gap analysis: identify which required disclosures have missing or incomplete data before the reporting deadline

### AI-Powered
- Narrative drafting: AI drafts qualitative disclosure sections from KPI data and structured initiative notes
- Completeness check: scan the assembled report and flag empty or below-minimum disclosures
- Language improvement: AI suggests more precise or compliant language for specific regulatory disclosure fields

---

## Data Model

```erDiagram
    esg_report_templates {
        ulid id PK
        ulid company_id FK
        string framework
        string name
        json disclosure_structure
        timestamps created_at_updated_at
    }

    esg_reports {
        ulid id PK
        ulid template_id FK
        ulid company_id FK
        string title
        integer reporting_year
        json disclosure_data
        string status
        string pdf_url
        integer version
        timestamp published_at
        timestamps created_at_updated_at
    }

    esg_report_templates ||--o{ esg_reports : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `esg_report_templates` | Framework disclosure structures | `id`, `company_id`, `framework`, `name`, `disclosure_structure` |
| `esg_reports` | Generated reports | `id`, `template_id`, `reporting_year`, `status`, `pdf_url`, `version` |

---

## Permissions

```
esg.reports.view
esg.reports.create
esg.reports.update
esg.reports.publish
esg.reports.export
```

---

## Filament

- **Resource:** `App\Filament\Esg\Resources\EsgReportResource`
- **Pages:** `ListEsgReports`, `CreateEsgReport`, `EditEsgReport`, `ViewEsgReport`
- **Custom pages:** `ReportBuilderPage`, `GapAnalysisPage`, `PublicReportPage`
- **Widgets:** `ReportingDeadlinesWidget`, `DisclosureCompletenessWidget`
- **Nav group:** Reporting

---

## Displaces

| Feature | FlowFlex | Plan A | Watershed | Sphera |
|---|---|---|---|---|
| GRI framework reporting | Yes | Yes | Partial | Yes |
| CSRD reporting | Yes | Yes | No | Yes |
| TCFD reporting | Yes | No | Yes | Yes |
| AI narrative drafting | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[carbon-footprints]] — emission data feeds environmental disclosures
- [[esg-kpis]] — KPI actuals map to framework disclosure fields
- [[sustainability-initiatives]] — initiatives referenced in narrative sections
- [[stakeholder-reporting]] — simplified version of ESG report for external audiences
