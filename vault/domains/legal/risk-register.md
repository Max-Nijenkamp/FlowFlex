---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.risk
status: planned
color: "#4ADE80"
---

# Risk Register

> Legal risk register for tracking regulatory, contractual, and operational legal risks with probability-impact scoring and mitigation tracking.

**Panel:** `legal`
**Module key:** `legal.risk`

## What It Does

The Legal Risk Register captures legal and compliance risks that are distinct from operational risks tracked in other modules. Each risk entry records what the risk is, where it comes from (regulatory change, contract exposure, litigation, IP infringement, data breach liability), its probability and impact scores, the current mitigation actions in place, and a residual risk rating. The register gives in-house counsel and senior management a single view of the company's legal exposure and whether it is improving or growing over time.

## Features

### Core
- Risk entry: title, description, risk category (regulatory, contractual, litigation, IP, data protection, employment, commercial), source, risk owner
- Probability × impact scoring: 1–5 scale for likelihood and consequence; system calculates inherent risk score and colour-codes (green/amber/red)
- Status: open, under mitigation, mitigated, accepted, closed
- Mitigation actions: record actions taken or planned to reduce the risk; each action has owner and due date
- Residual risk: post-mitigation probability × impact score; compare to inherent to show reduction
- Review schedule: periodic review date per risk entry; alert owner before review is due

### Advanced
- Risk history: record how probability and impact scores have changed over time; visualise improvement or deterioration
- Risk linking: link a risk to a related contract, matter, obligation, or regulatory tracking entry
- Risk appetite: configure company risk appetite level (low/medium/high tolerance) per risk category; highlight risks exceeding appetite
- Executive summary view: top 10 risks by inherent score for board or executive reporting
- Risk heat map: 5×5 probability-impact matrix with each risk plotted as a data point
- Bulk import: migrate existing risk register from a spreadsheet via CSV import

### AI-Powered
- Risk description assist: suggest standardised risk descriptions and categories based on free-text input
- Mitigation suggestions: based on risk category and description, recommend common mitigation approaches

## Data Model

```erDiagram
    legal_risks {
        ulid id PK
        ulid company_id FK
        string title
        text description
        string category
        string source
        ulid risk_owner_id FK
        integer probability
        integer impact
        integer inherent_score
        integer residual_probability
        integer residual_impact
        integer residual_score
        string status
        date next_review_date
        timestamps timestamps
    }

    legal_risk_mitigations {
        ulid id PK
        ulid risk_id FK
        text description
        ulid owner_id FK
        date due_date
        string status
        timestamps timestamps
    }

    legal_risks ||--o{ legal_risk_mitigations : "mitigated by"
```

| Table | Purpose |
|---|---|
| `legal_risks` | Risk entries with scoring and status |
| `legal_risk_mitigations` | Mitigation actions per risk |

## Permissions

```
legal.risk.view-any
legal.risk.create
legal.risk.update
legal.risk.manage-mitigations
legal.risk.delete
```

## Filament

**Resource class:** `LegalRiskResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `RiskHeatMapPage` (5×5 probability-impact matrix visualisation)
**Widgets:** `TopRisksWidget` (top 5 inherent risks), `RisksExceedingAppetiteWidget`
**Nav group:** Matters

## Displaces

| Competitor | Feature Replaced |
|---|---|
| LogicGate (risk module) | Legal risk register and scoring |
| Riskonnect | Enterprise legal risk management |
| Resolver GRC | Risk register and mitigation tracking |
| MetricStream (SMB) | Compliance and legal risk management |

## Related

- [[contracts]] — contract exposure risks linked here
- [[matter-management]] — litigation risk linked to open matters
- [[compliance-calendar]] — regulatory non-compliance risks captured here
- [[regulatory-tracking]] — new regulations generate new risk entries
