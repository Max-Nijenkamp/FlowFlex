---
type: module
domain: ESG & Sustainability
panel: esg
module-key: esg.stakeholder-reports
status: planned
color: "#4ADE80"
---

# Stakeholder Reporting

> External stakeholder ESG summaries — simplified, investor and customer-facing published reports with a shareable public URL.

**Panel:** `esg`
**Module key:** `esg.stakeholder-reports`

---

## What It Does

Stakeholder Reporting produces simplified, non-technical ESG summaries intended for external audiences — investors, customers, employees, and the general public. Unlike the full regulatory ESG reports, these are designed for readability and brand alignment rather than regulatory compliance. They pull headline metrics from the ESG KPI and carbon data, include initiative highlights, and are published as a public web page with a shareable URL. They can also be exported as a branded PDF for inclusion in investor packs and RFP responses.

---

## Features

### Core
- Report creation: title, period, audience type (investor, customer, employee, public)
- Metric selection: choose which ESG KPIs and carbon figures to highlight
- Narrative sections: rich text blocks for company ESG narrative and CEO statement
- Initiative highlights: include selected sustainability initiatives as progress stories
- Public URL: publish the report at a shareable URL; no login required to view
- PDF export: branded PDF version for offline distribution

### Advanced
- Report branding: apply company logo, brand colours, and custom cover image
- Comparison to prior year: include previous period actuals alongside current year for trend context
- Third-party verification badge: display an assurance badge if data has been independently verified
- Multiple audiences: create separate reports for investors, customers, and employees with different data selections
- Scheduled update: automatically refresh live metric values in a published report on a schedule

### AI-Powered
- Audience-appropriate language: AI adjusts the tone and complexity of narrative copy for the selected audience
- Headline metric suggestions: recommend the three most impactful metrics to lead with for a given audience
- Report comparison: identify how this report's claims compare to a previous published version

---

## Data Model

```erDiagram
    stakeholder_reports {
        ulid id PK
        ulid company_id FK
        string title
        string audience_type
        integer reporting_year
        json selected_kpi_ids
        json narrative_sections
        boolean is_published
        string public_url_slug
        string pdf_url
        timestamp published_at
        timestamps created_at_updated_at
    }

    stakeholder_reports }o--|| companies : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `stakeholder_reports` | Stakeholder report records | `id`, `company_id`, `title`, `audience_type`, `is_published`, `public_url_slug`, `pdf_url` |

---

## Permissions

```
esg.stakeholder-reports.view
esg.stakeholder-reports.create
esg.stakeholder-reports.update
esg.stakeholder-reports.publish
esg.stakeholder-reports.export
```

---

## Filament

- **Resource:** `App\Filament\Esg\Resources\StakeholderReportResource`
- **Pages:** `ListStakeholderReports`, `CreateStakeholderReport`, `EditStakeholderReport`
- **Custom pages:** `StakeholderReportBuilderPage`, `PublicStakeholderReportPage` (unauthenticated)
- **Widgets:** `PublishedReportsWidget`, `ReportViewsWidget`
- **Nav group:** Reporting

---

## Displaces

| Feature | FlowFlex | Plan A | Bain ESG tools | Manual PDF |
|---|---|---|---|---|
| Audience-targeted reports | Yes | Partial | No | Manual |
| Public URL publishing | Yes | No | No | No |
| Live metric refresh | Yes | No | No | No |
| AI audience tone adjustment | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[esg-reports]] — stakeholder reports draw from the same underlying data as regulatory reports
- [[esg-kpis]] — headline metrics selected from the KPI library
- [[carbon-footprints]] — carbon targets and actuals included in investor summaries
- [[sustainability-initiatives]] — initiative highlights included as progress stories
