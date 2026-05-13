---
type: module
domain: Whistleblowing & Ethics
panel: ethics
module-key: ethics.actions
status: planned
color: "#4ADE80"
---

# Investigator Actions

> Structured investigation actions — interviews, evidence review, third-party requests, and escalation records during an active case.

**Panel:** `ethics`
**Module key:** `ethics.actions`

---

## What It Does

Investigator Actions provides a structured log of the discrete actions taken during an investigation, beyond free-text case notes. An investigator records each formal action — an interview conducted, a document reviewed, a request sent to a third party, an escalation to the ethics committee — with the date, participants, outcome, and supporting documents. This creates the evidentiary record of how the investigation was conducted, which is essential for demonstrating procedural fairness in regulatory or legal proceedings.

---

## Features

### Core
- Action types: interview, document review, evidence collected, third-party request, escalation, legal referral, committee presentation
- Action record: date, participants, description, outcome notes, and supporting document upload
- Chronological action log: ordered view of all actions taken in a case
- Action status: planned, in progress, complete
- Interview scheduling: log a planned interview with date, interviewee (name only, not linked for anonymity), and outcome recorded after

### Advanced
- Interview transcript upload: attach a transcript or audio recording to an interview action
- Third-party request tracking: log requests sent to external parties (e.g. HR records, IT forensics) with response status
- Committee escalation: record formal escalation to the ethics or disciplinary committee with submission date and response
- Action due dates: assign a due date to planned actions for investigation timeline management
- Witness list: record witnesses involved in the investigation without exposing their identities to other investigators

### AI-Powered
- Action suggestion: AI recommends next investigation actions based on the case category and progress
- Interview question drafts: AI suggests relevant questions for a planned interview based on the case facts
- Evidence gap detection: AI identifies whether standard evidence types for the category have not yet been collected

---

## Data Model

```erDiagram
    investigator_actions {
        ulid id PK
        ulid case_id FK
        ulid investigator_id FK
        string action_type
        date action_date
        date due_date
        string status
        text description
        text outcome_notes
        json participant_names
        json attachment_urls
        timestamps created_at_updated_at
    }

    investigator_actions }o--|| ethics_cases : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `investigator_actions` | Investigation action records | `id`, `case_id`, `investigator_id`, `action_type`, `action_date`, `status`, `outcome_notes` |

---

## Permissions

```
ethics.actions.view-assigned
ethics.actions.create
ethics.actions.update
ethics.actions.delete
ethics.actions.view-all
```

---

## Filament

- **Resource:** `App\Filament\Ethics\Resources\InvestigatorActionResource`
- **Pages:** `ListInvestigatorActions`, `CreateInvestigatorAction`, `ViewInvestigatorAction`
- **Custom pages:** `CaseActionTimelinePage`
- **Widgets:** `PlannedActionsWidget`, `OverdueActionsWidget`
- **Nav group:** Investigations

---

## Displaces

| Feature | FlowFlex | NAVEX | Vault Platform | EthicsPoint |
|---|---|---|---|---|
| Structured action logging | Yes | Yes | Yes | Partial |
| Interview record | Yes | Yes | Yes | Yes |
| AI action suggestions | Yes | No | No | No |
| Evidence gap detection | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[case-management]] — actions logged against investigation cases
- [[resolution-outcomes]] — action log informs the resolution record
