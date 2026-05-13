---
type: module
domain: Professional Services (PSA)
panel: psa
module-key: psa.client-reports
status: planned
color: "#4ADE80"
---

# Client Reporting

> Client-facing project status reports built with a visual report builder and shared via a secure link or PDF export.

**Panel:** `psa`
**Module key:** `psa.client-reports`

---

## What It Does

Client Reporting allows project managers to produce professional, branded project status reports for their clients without leaving FlowFlex. A drag-and-drop report builder assembles sections from live project data — phase status, deliverable completion, hours burned, upcoming milestones, and risk summary — and applies the company's brand. Reports can be shared as a secure web link (read-only, optionally password-protected) or exported as a PDF. Clients do not need a FlowFlex account to view their reports.

---

## Features

### Core
- Report template builder: drag-and-drop layout with predefined section blocks
- Section types: phase status table, deliverable progress bar, time burned vs budget, milestone timeline, risk log, free-text narrative
- Live data binding: sections pull current data from the linked project automatically
- PDF export: generate a branded PDF version of the report
- Secure link sharing: generate a unique read-only URL for the client with optional expiry

### Advanced
- Report scheduling: automatically generate and email a status report on a recurring schedule (weekly, bi-weekly)
- Version history: access previously generated reports for audit trail
- Custom branding: apply client-specific logo and colour theme to the report template
- Multiple templates: create different report templates for different engagement types
- Password protection: add a password to the secure link for additional confidentiality

### AI-Powered
- Narrative generation: AI drafts the executive summary and status commentary from project data
- Risk highlight suggestion: AI surfaces the most important risks to include in the report
- Report completeness check: flag when key sections are empty before sharing with the client

---

## Data Model

```erDiagram
    report_templates {
        ulid id PK
        ulid company_id FK
        string name
        json layout
        string branding_config
        timestamps created_at_updated_at
    }

    client_reports {
        ulid id PK
        ulid project_id FK
        ulid template_id FK
        ulid company_id FK
        string title
        date report_date
        json content_snapshot
        string share_token
        datetime share_expires_at
        boolean is_password_protected
        string pdf_url
        timestamps created_at_updated_at
    }

    report_templates ||--o{ client_reports : "used by"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `report_templates` | Report layouts | `id`, `company_id`, `name`, `layout`, `branding_config` |
| `client_reports` | Generated reports | `id`, `project_id`, `template_id`, `report_date`, `share_token`, `pdf_url` |

---

## Permissions

```
psa.client-reports.create
psa.client-reports.view-any
psa.client-reports.share
psa.client-reports.manage-templates
psa.client-reports.export
```

---

## Filament

- **Resource:** None (custom page only)
- **Pages:** N/A
- **Custom pages:** `ReportBuilderPage`, `ClientReportListPage`, `ReportPreviewPage`, `PublicReportViewPage` (unauthenticated)
- **Widgets:** `RecentReportsWidget`, `SharedReportViewsWidget`
- **Nav group:** Delivery

---

## Displaces

| Feature | FlowFlex | Teamwork | Mavenlink | PowerPoint reports |
|---|---|---|---|---|
| Live-data report builder | Yes | Partial | Yes | No |
| Secure client link | Yes | No | Yes | No |
| Scheduled auto-generation | Yes | No | No | No |
| AI narrative generation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[project-delivery]] — report data sourced from project phases and deliverables
- [[time-billing]] — hours burned data included in reports
- [[profitability]] — budget vs actual summary in reports
