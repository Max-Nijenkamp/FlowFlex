---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.matters
status: planned
color: "#4ADE80"
---

# Matter Management

> Track legal matters, manage outside counsel, record costs, and organise matter documents from dispute to resolution.

**Panel:** `legal`
**Module key:** `legal.matters`

## What It Does

Matter Management gives in-house legal teams a structured workspace for each legal matter — whether that is a customer dispute, regulatory investigation, employment claim, litigation, or advisory project. Each matter record tracks the matter type, parties involved, assigned internal and external counsel, status, key dates, cost budget and actuals, and all associated documents. Invoices from outside counsel are logged against the matter budget so legal spend is visible without waiting for quarterly reporting.

## Features

### Core
- Matter record: title, matter number, type (litigation, regulatory, employment, advisory, IP, contract dispute), status, open date, jurisdiction
- Matter status: open → active → under negotiation → resolved → closed; with closure date and outcome
- Parties: internal owner (in-house lawyer), outside counsel firm and contact, opposing parties, and other stakeholders
- Document management: upload and organise matter documents (pleadings, correspondence, evidence, settlement agreements) with version control
- Key dates: court hearing dates, filing deadlines, statute of limitations — with calendar alerts before each date
- Related contracts: link to contracts from [[contracts]] that are the subject of the matter

### Advanced
- Outside counsel management: track law firm name, matter-specific contact, agreed fee arrangement (hourly, fixed, retainer), and billing currency
- Cost tracking: log outside counsel invoices against the matter; compare actual spend to budget; spend-to-date alert when >80% of budget consumed
- Matter budget: set total budget per matter; track monthly actuals
- Activity log: timestamped notes and actions taken on the matter; internal to the legal team
- Matter templates: pre-configured matter structure for common matter types (employment claim, IP dispute, standard commercial dispute)
- Conflict check: flag if an outside counsel firm has previously acted for a counterparty in another matter

### AI-Powered
- Matter summary: auto-generate a plain-language matter summary from the notes and document titles attached to the record
- Risk assessment: based on matter type, jurisdiction, and opposing party, suggest a likelihood and potential exposure estimate

## Data Model

```erDiagram
    legal_matters {
        ulid id PK
        ulid company_id FK
        string matter_number
        string title
        string matter_type
        string status
        string jurisdiction
        date opened_on
        date closed_on
        string outcome
        ulid internal_owner_id FK
        decimal budget
        decimal actual_spend
        timestamps timestamps
        softDeletes deleted_at
    }

    legal_matter_parties {
        ulid id PK
        ulid matter_id FK
        string role
        string party_name
        string contact_email
        string firm_name
        string fee_arrangement
    }

    legal_matter_costs {
        ulid id PK
        ulid matter_id FK
        string description
        string vendor
        decimal amount
        string currency
        date invoice_date
        string status
        timestamps timestamps
    }

    legal_matters ||--o{ legal_matter_parties : "involves"
    legal_matters ||--o{ legal_matter_costs : "incurs"
```

| Table | Purpose |
|---|---|
| `legal_matters` | Matter header with type, status, and budget |
| `legal_matter_parties` | Counsel and party contacts per matter |
| `legal_matter_costs` | Outside counsel invoices and other costs |

## Permissions

```
legal.matters.view-any
legal.matters.create
legal.matters.update
legal.matters.manage-costs
legal.matters.close
```

## Filament

**Resource class:** `MatterResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `MatterCostTrackerPage` (budget vs actual with invoice log)
**Widgets:** `ActiveMattersWidget`, `LegalSpendWidget` (YTD outside counsel spend)
**Nav group:** Matters

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Clio (in-house) | Matter tracking for in-house legal teams |
| HighQ | Legal matter management and collaboration |
| LegalTracker | Outside counsel management and spend |
| SimpleLegal | Matter management and invoice review |

## Related

- [[contracts]] — contracts linked as subject of a matter
- [[risk-register]] — matter risk recorded in legal risk register
- [[e-signatures]] — settlement agreements executed via e-signature
- [[../finance/INDEX]] — outside counsel costs reconciled against finance
