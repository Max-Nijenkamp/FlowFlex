---
type: module
domain: Whistleblowing & Ethics
panel: ethics
module-key: ethics.cases
status: planned
color: "#4ADE80"
---

# Case Management

> Investigation case management — assigned investigator, case notes, timeline of actions, and case status tracking.

**Panel:** `ethics`
**Module key:** `ethics.cases`

---

## What It Does

Case Management provides the investigator's workspace for managing an ethics investigation from intake to resolution. When an incident report is received, it is promoted to a case and assigned to an investigator. The case record tracks the investigation timeline — every note, action, interview, and decision is logged chronologically. The case status moves through defined stages (open, under investigation, on hold, resolved), and access is restricted to the assigned investigator and ethics officer to maintain confidentiality.

---

## Features

### Core
- Case creation: promoted from an incident report, with reference to the originating report
- Investigator assignment: assign the case to a named investigator with a target resolution date
- Case notes: private chronological notes visible only to the investigator and ethics officer
- Status workflow: open → under investigation → on hold → resolved → closed
- Confidentiality flag: case access restricted to assigned investigator and ethics officer
- Target resolution date: SLA tracking with alerts if the case is approaching overdue

### Advanced
- Case team: add additional investigators or subject matter experts with view-only access
- Related cases: link cases that share subjects or circumstances
- Case hold: pause the investigation clock for legitimate reasons (e.g. waiting for legal advice) with a reason
- External counsel: flag cases referred to external legal counsel with counsel contact details
- Anonymised case summary: generate an anonymised case summary safe to share in aggregate reporting

### AI-Powered
- Investigation checklist suggestion: AI recommends investigation steps based on the incident category
- Case summary: AI drafts a plain-language case summary from structured case data
- Pattern detection: identify whether a subject appears across multiple cases indicating systemic behaviour

---

## Data Model

```erDiagram
    ethics_cases {
        ulid id PK
        ulid report_id FK
        ulid company_id FK
        ulid investigator_id FK
        string case_reference
        string status
        date target_resolution_date
        boolean is_confidential
        boolean on_legal_hold
        text hold_reason
        timestamp resolved_at
        timestamps created_at_updated_at
    }

    case_notes {
        ulid id PK
        ulid case_id FK
        ulid author_id FK
        text body
        json attachment_urls
        timestamp created_at
    }

    ethics_cases ||--o{ case_notes : "documented via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `ethics_cases` | Investigation cases | `id`, `report_id`, `investigator_id`, `case_reference`, `status`, `target_resolution_date` |
| `case_notes` | Chronological notes | `id`, `case_id`, `author_id`, `body`, `created_at` |

---

## Permissions

```
ethics.cases.view-assigned
ethics.cases.view-all
ethics.cases.manage
ethics.cases.assign-investigator
ethics.cases.view-audit-log
```

---

## Filament

- **Resource:** `App\Filament\Ethics\Resources\EthicsCaseResource`
- **Pages:** `ListEthicsCases`, `CreateEthicsCase`, `ViewEthicsCase`
- **Custom pages:** `CaseTimelinePage`, `CaseWorkspacePage`
- **Widgets:** `OpenCasesWidget`, `OverdueCasesWidget`
- **Nav group:** Investigations

---

## Displaces

| Feature | FlowFlex | NAVEX | EthicsPoint | Vault Platform |
|---|---|---|---|---|
| Investigator-only access | Yes | Yes | Yes | Yes |
| Case timeline | Yes | Yes | Yes | Yes |
| SLA tracking | Yes | Yes | Yes | Yes |
| AI investigation checklist | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[incident-reports]] — cases promoted from incident reports
- [[investigator-actions]] — specific actions logged against the case
- [[resolution-outcomes]] — case resolution recorded and linked
- [[reporting-analytics]] — case data feeds programme metrics
