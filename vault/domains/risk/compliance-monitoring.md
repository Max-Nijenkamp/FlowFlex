---
type: module
domain: Risk Management
panel: risk
module-key: risk.compliance
status: planned
color: "#4ADE80"
---

# Compliance Monitoring

> Regulatory obligation tracking — log compliance requirements, assign tasks, collect evidence, and monitor status.

**Panel:** `risk`
**Module key:** `risk.compliance`

---

## What It Does

Compliance Monitoring tracks the company's regulatory and legal obligations across frameworks such as GDPR, ISO 27001, SOC 2, Bribery Act, and industry-specific regulations. Each obligation is documented, assigned to a compliance owner, linked to a due date, and supported by evidence tasks. The system tracks the status of each obligation (compliant, non-compliant, in-progress, not applicable) and provides a compliance calendar of upcoming deadlines. Evidence files can be attached to each obligation and reviewed by the compliance manager or external auditor.

---

## Features

### Core
- Obligation register: obligation name, regulatory framework, description, owner, and due date
- Regulatory frameworks: configure the frameworks the company is subject to (GDPR, ISO 27001, SOC 2, etc.)
- Compliance status: compliant, in-progress, non-compliant, not applicable, exempt
- Evidence collection: attach files, links, or notes as evidence of compliance
- Compliance tasks: break an obligation into discrete tasks with assignee and due date
- Compliance calendar: view all upcoming obligation deadlines in a calendar view

### Advanced
- Framework mapping: link obligations across multiple frameworks that share a common control (e.g. GDPR Art. 32 and ISO 27001 A.12.1)
- Obligation inheritance: define a library of standard obligations for a framework and import them for new companies
- External audit support: generate an auditor evidence pack — all obligations with their attached evidence — as a structured ZIP export
- Review workflow: compliance manager reviews and sign-off of each obligation before it is marked compliant
- Breach logging: log a compliance failure with severity, root cause, and remediation action
- Exemption management: mark an obligation as not applicable with a documented justification

### AI-Powered
- Regulation change alerts: notify when a monitored regulatory framework publishes a change that may affect logged obligations
- Compliance gap identification: analyse the obligation register and flag obligations with no evidence or past-due tasks
- Remediation prioritisation: rank non-compliant obligations by regulatory exposure and suggest the order of remediation

---

## Data Model

```erDiagram
    compliance_frameworks {
        ulid id PK
        ulid company_id FK
        string name
        string regulator
        string description
        timestamps created_at_updated_at
    }

    compliance_obligations {
        ulid id PK
        ulid company_id FK
        ulid framework_id FK
        ulid owner_id FK
        string reference
        string title
        text description
        string status
        date due_date
        date last_reviewed_date
        timestamps created_at_updated_at
    }

    compliance_tasks {
        ulid id PK
        ulid company_id FK
        ulid obligation_id FK
        ulid assignee_id FK
        string title
        string status
        date due_date
        timestamps created_at_updated_at
    }

    compliance_evidence {
        ulid id PK
        ulid company_id FK
        ulid obligation_id FK
        ulid uploaded_by FK
        string file_name
        string file_url
        text notes
        date evidence_date
        timestamps created_at_updated_at
    }

    compliance_frameworks ||--o{ compliance_obligations : "contains"
    compliance_obligations ||--o{ compliance_tasks : "broken into"
    compliance_obligations ||--o{ compliance_evidence : "evidenced by"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `compliance_frameworks` | Regulatory frameworks | `id`, `company_id`, `name`, `regulator` |
| `compliance_obligations` | Individual obligations | `id`, `company_id`, `framework_id`, `owner_id`, `reference`, `status`, `due_date` |
| `compliance_tasks` | Sub-tasks per obligation | `id`, `obligation_id`, `assignee_id`, `title`, `status`, `due_date` |
| `compliance_evidence` | Evidence files and notes | `id`, `obligation_id`, `uploaded_by`, `file_url`, `evidence_date` |

---

## Permissions

```
risk.compliance.view-own
risk.compliance.view-all
risk.compliance.manage-obligations
risk.compliance.upload-evidence
risk.compliance.export
```

---

## Filament

- **Resource:** `App\Filament\Risk\Resources\ComplianceObligationResource`
- **Pages:** `ListComplianceObligations`, `CreateComplianceObligation`, `EditComplianceObligation`, `ViewComplianceObligation`
- **Custom pages:** `ComplianceCalendarPage`, `FrameworkOverviewPage`, `AuditEvidencePackPage`
- **Widgets:** `ObligationsDueWidget`, `NonCompliantObligationsWidget`
- **Nav group:** Compliance

---

## Displaces

| Feature | FlowFlex | Archer | LogicManager | ServiceNow GRC |
|---|---|---|---|---|
| Obligation register | Yes | Yes | Yes | Yes |
| Evidence collection | Yes | Yes | Yes | Yes |
| Framework mapping | Yes | Yes | Yes | Yes |
| AI regulation change alerts | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[risk-register]] — non-compliance issues may create risk register entries
- [[risk-controls]] — controls mapped to regulatory obligations for evidence
- [[risk-reporting]] — compliance status included in board risk reports
- [[ethics/policy-acknowledgments]] — policy sign-off evidence feeds GDPR and ethics compliance obligations
- [[legal/INDEX]] — legal team manages regulatory framework obligations
