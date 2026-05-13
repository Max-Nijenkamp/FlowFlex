---
type: module
domain: Whistleblowing & Ethics
panel: ethics
module-key: ethics.incidents
status: planned
color: "#4ADE80"
---

# Incident Reports

> Anonymous incident report intake — category, description, evidence upload, and anonymous two-way communication via a secure tracking code.

**Panel:** `ethics`
**Module key:** `ethics.incidents`

---

## What It Does

Incident Reports provides the secure, anonymous channel through which employees, contractors, and external parties can raise concerns about misconduct, fraud, safety violations, or other ethical issues. The reporter submits a form without creating an account — selecting a category, providing a description, and optionally uploading evidence. The system generates a unique anonymous tracking code the reporter can use to check the status of their report and receive questions from the investigator without revealing their identity. All report data is end-to-end encrypted.

---

## Features

### Core
- Anonymous submission form: category (fraud, harassment, safety, conflict of interest, bribery, other), description, evidence upload
- Anonymous tracking code: unique alphanumeric code provided to the reporter for follow-up access
- Two-way anonymous communication: investigator can ask clarifying questions; reporter responds anonymously
- Evidence upload: documents, screenshots, and audio files attached to the report
- Report acknowledgment: automatic acknowledgment sent via tracking code upon submission
- Incident category management: admin-configurable list of report categories

### Advanced
- Named reporting option: reporter can optionally provide their identity for a non-anonymous submission
- Multi-language support: report form available in multiple configured languages
- External reporter access: the report form can be accessed without a FlowFlex login for third parties
- Urgency flagging: reporter can mark a report as urgent; immediate notification to the ethics officer
- Duplicate linking: investigators can link related reports on the same incident

### AI-Powered
- Category suggestion: AI suggests the most relevant category based on the description text
- Severity pre-screening: AI provides an initial severity assessment to help triage prioritisation
- Duplicate detection: flag reports that appear to describe the same incident as a recent submission

---

## Data Model

```erDiagram
    incident_reports {
        ulid id PK
        ulid company_id FK
        string tracking_code
        string category
        text description
        boolean is_anonymous
        string reporter_name_encrypted
        string reporter_email_encrypted
        string urgency
        string status
        json evidence_urls
        timestamp submitted_at
        timestamps created_at_updated_at
    }

    incident_messages {
        ulid id PK
        ulid report_id FK
        string author_type
        text body
        json attachment_urls
        timestamp created_at
    }

    incident_reports ||--o{ incident_messages : "communicates via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `incident_reports` | Submitted reports | `id`, `company_id`, `tracking_code`, `category`, `description`, `is_anonymous`, `urgency`, `status` |
| `incident_messages` | Two-way messages | `id`, `report_id`, `author_type`, `body` |

---

## Permissions

```
ethics.incidents.view-reports
ethics.incidents.manage-categories
ethics.incidents.respond-to-reporter
ethics.incidents.export
ethics.incidents.view-statistics
```

---

## Filament

- **Resource:** `App\Filament\Ethics\Resources\IncidentReportResource`
- **Pages:** `ListIncidentReports`, `ViewIncidentReport`
- **Custom pages:** `ReporterPortalPage` (unauthenticated tracking code access), `IncidentInboxPage`
- **Widgets:** `NewReportsWidget`, `UrgentReportsWidget`
- **Nav group:** Reports

---

## Displaces

| Feature | FlowFlex | NAVEX | EthicsPoint | Vault Platform |
|---|---|---|---|---|
| Anonymous report intake | Yes | Yes | Yes | Yes |
| Two-way anonymous communication | Yes | Yes | Yes | Yes |
| Evidence upload | Yes | Yes | Yes | Yes |
| AI severity pre-screening | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[case-management]] — submitted reports are assigned to investigation cases
- [[reporting-analytics]] — report volumes and categories feed analytics
