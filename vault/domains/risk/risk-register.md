---
type: module
domain: Risk Management
panel: risk
module-key: risk.register
status: planned
color: "#4ADE80"
---

# Risk Register

> Enterprise risk identification and documentation — risk owner, category, description, status, and linked controls.

**Panel:** `risk`
**Module key:** `risk.register`

---

## What It Does

The Risk Register is the central repository of all identified enterprise risks. Risk managers and department heads log risks across categories — strategic, operational, financial, legal, technology, and people. Each risk entry captures the description, owner, category, source, and current status (open, in-treatment, closed). Risks are linked to related controls, assessments, and compliance obligations. The register provides a single auditable source of truth for the company's risk landscape that can be presented to the board or external auditors.

---

## Features

### Core
- Risk entry: title, description, category, owner (user), department, source, and date identified
- Risk categories: strategic, operational, financial, legal, technology, people, reputational (configurable)
- Risk status: identified → in-treatment → accepted → closed
- Risk ownership: assign a risk owner responsible for treatment and review
- Tags: free-text tags for filtering and grouping
- Audit trail: full history of changes to each risk record including field-level change log

### Advanced
- Related risks: link risks that share a common cause or impact for dependency tracking
- Review schedule: set a periodic review date per risk; alert owner when review is due
- Risk appetite statement: define and display the company's risk appetite per category
- Document attachments: attach supporting evidence, policy documents, or incident reports
- Risk escalation: escalate a risk to board-level visibility with a single action
- Custom fields: configure additional fields per risk category

### AI-Powered
- Duplicate detection: flag when a newly submitted risk is similar to an existing register entry
- Category suggestion: AI recommends the most appropriate category based on the risk description
- Emerging risk alerts: scan internal data and optionally external news feeds to surface new risk themes

---

## Data Model

```erDiagram
    risks {
        ulid id PK
        ulid company_id FK
        ulid owner_id FK
        string title
        text description
        string category
        string department
        string source
        string status
        date identified_date
        date next_review_date
        boolean board_level
        json tags
        timestamps created_at_updated_at
    }

    risk_relations {
        ulid id PK
        ulid company_id FK
        ulid risk_id FK
        ulid related_risk_id FK
        string relation_type
        timestamps created_at_updated_at
    }

    risks ||--o{ risk_relations : "links to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `risks` | Risk register entries | `id`, `company_id`, `owner_id`, `title`, `category`, `department`, `status`, `next_review_date`, `board_level` |
| `risk_relations` | Risk interdependency links | `id`, `risk_id`, `related_risk_id`, `relation_type` |

---

## Permissions

```
risk.register.view-own-department
risk.register.view-all
risk.register.create
risk.register.manage
risk.register.escalate
```

---

## Filament

- **Resource:** `App\Filament\Risk\Resources\RiskResource`
- **Pages:** `ListRisks`, `CreateRisk`, `EditRisk`, `ViewRisk`
- **Custom pages:** `RiskRegisterBoardViewPage`
- **Widgets:** `OpenRisksWidget`, `RisksDueForReviewWidget`
- **Nav group:** Risks

---

## Displaces

| Feature | FlowFlex | Archer | LogicManager | ServiceNow GRC |
|---|---|---|---|---|
| Risk register | Yes | Yes | Yes | Yes |
| Risk ownership and review | Yes | Yes | Yes | Yes |
| Audit trail | Yes | Yes | Yes | Yes |
| AI duplicate detection | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[risk-assessments]] — each risk has one or more assessments for scoring
- [[risk-controls]] — controls linked to risks to show treatment
- [[risk-reporting]] — risk register data feeds board reports
- [[compliance-monitoring]] — regulatory obligations may create risk register entries
