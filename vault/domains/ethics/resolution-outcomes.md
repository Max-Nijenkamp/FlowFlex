---
type: module
domain: Whistleblowing & Ethics
panel: ethics
module-key: ethics.outcomes
status: planned
color: "#4ADE80"
---

# Resolution Outcomes

> Case resolution records — outcome type, corrective actions, closure notes, and appeal option.

**Panel:** `ethics`
**Module key:** `ethics.outcomes`

---

## What It Does

Resolution Outcomes captures the formal conclusion of an ethics investigation. Once the investigator has completed their work, they record the outcome — whether the allegation was substantiated, partially substantiated, or not substantiated — document the corrective or disciplinary actions recommended, and close the case with a closure note. The anonymous reporter is notified via their tracking code that the case has been resolved (without revealing investigation details). If the reporter disagrees with the outcome, an appeal can be submitted for review by a senior ethics officer.

---

## Features

### Core
- Outcome recording: outcome type (substantiated, partially substantiated, not substantiated, referred to law enforcement, unable to conclude)
- Corrective actions: list of recommended actions with owner and due date (training, disciplinary, policy change, process improvement)
- Closure notes: written summary of the investigation findings (redacted for anonymity where required)
- Reporter notification: anonymous notification to the reporter via tracking code that the case is closed
- Outcome date: formal date of case resolution recorded for SLA compliance

### Advanced
- Appeal submission: reporter can submit an appeal via tracking code; creates an appeal case for senior review
- Appeal outcome: senior ethics officer records the appeal decision and rationale
- Corrective action tracking: track completion of each recommended corrective action with deadline
- Outcome report (redacted): generate an anonymised outcome summary for audit or board committee use
- Outcome statistics: track outcomes by category for programme effectiveness reporting

### AI-Powered
- Closure notes drafting: AI drafts the closure summary from case notes and action log
- Outcome consistency check: flag when the proposed outcome differs significantly from historical outcomes for similar cases
- Corrective action suggestions: AI recommends corrective actions based on the outcome type and case category

---

## Data Model

```erDiagram
    case_resolutions {
        ulid id PK
        ulid case_id FK
        ulid company_id FK
        string outcome_type
        text closure_notes
        date resolved_date
        boolean appeal_available
        timestamps created_at_updated_at
    }

    corrective_actions {
        ulid id PK
        ulid resolution_id FK
        string action_description
        string action_type
        ulid owner_id FK
        date due_date
        string status
        timestamps created_at_updated_at
    }

    case_appeals {
        ulid id PK
        ulid resolution_id FK
        string tracking_code
        text appeal_reason
        string appeal_status
        text appeal_decision
        ulid reviewed_by FK
        timestamp reviewed_at
        timestamps created_at_updated_at
    }

    case_resolutions ||--o{ corrective_actions : "requires"
    case_resolutions ||--o{ case_appeals : "appealed via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `case_resolutions` | Case outcomes | `id`, `case_id`, `outcome_type`, `closure_notes`, `resolved_date` |
| `corrective_actions` | Required actions | `id`, `resolution_id`, `action_description`, `action_type`, `due_date`, `status` |
| `case_appeals` | Appeal records | `id`, `resolution_id`, `appeal_reason`, `appeal_status`, `appeal_decision` |

---

## Permissions

```
ethics.outcomes.record
ethics.outcomes.view
ethics.outcomes.manage-appeals
ethics.outcomes.track-corrective-actions
ethics.outcomes.export
```

---

## Filament

- **Resource:** `App\Filament\Ethics\Resources\CaseResolutionResource`
- **Pages:** `ListCaseResolutions`, `CreateCaseResolution`, `ViewCaseResolution`
- **Custom pages:** `CorrectiveActionTrackerPage`, `AppealQueuePage`
- **Widgets:** `OutcomeBreakdownWidget`, `OverdueCorrectiveActionsWidget`
- **Nav group:** Investigations

---

## Displaces

| Feature | FlowFlex | NAVEX | Vault Platform | EthicsPoint |
|---|---|---|---|---|
| Outcome type classification | Yes | Yes | Yes | Yes |
| Corrective action tracking | Yes | Yes | Yes | Partial |
| Anonymous reporter notification | Yes | Yes | Yes | Yes |
| Appeal workflow | Yes | Yes | Yes | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[case-management]] — resolution recorded against the investigation case
- [[investigator-actions]] — action log informs the resolution notes
- [[reporting-analytics]] — outcome types feed programme analytics
