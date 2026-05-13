---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.contracts
status: planned
color: "#4ADE80"
---

# Contracts

> Full contract lifecycle from intake through execution, obligation tracking, and expiry management — with AI-powered clause extraction.

**Panel:** `legal`
**Module key:** `legal.contracts`

## What It Does

Contracts is the central repository for every agreement the company enters into — customer contracts, supplier agreements, NDAs, employment contracts, partnership agreements, and leases. Each contract record captures structured metadata (parties, value, term, renewal date, auto-renew flag) alongside the document itself. Status workflows track a contract from draft through negotiation and execution to active, expired, or renewed. Obligation tracking ensures key deadlines are not missed. Renewal alerts prevent unintentional auto-renewals or missed renegotiation windows.

## Features

### Core
- Contract intake: upload PDF or create from template; capture metadata (title, type, parties, value, currency, start date, end date, auto-renew, notice period days)
- Contract types: customer, supplier, employment, NDA, partnership, lease, service agreement, other
- Status workflow: draft → review → negotiation → executed → active → expiring → expired/renewed
- Party management: link contract parties to CRM contacts/companies or supplier records in [[../operations/supplier-management]]
- Expiry dashboard: contracts expiring in 30, 60, and 90 days with owner and renewal action needed
- Document versioning: store each version with a timestamp and change note; full version history accessible

### Advanced
- Obligation tracking: extract key obligations (payment milestones, delivery deadlines, reporting requirements) as tasks with assigned owner and due date
- Renewal reminders: configurable notification schedule before expiry (90, 60, 30, 14, 7 days)
- Clause library: approved standard clause variants stored for use during negotiation redlining
- Contract value tracking: total committed spend or revenue per contract; portfolio view of total contract value by type
- Linked contracts: relate contracts to each other (master agreement → statement of work, NDA → customer contract)
- Contract search: full-text search across titles, party names, and extracted metadata

### AI-Powered
- Clause extraction: auto-extract parties, effective date, expiry date, payment terms, termination clauses, and governing law from uploaded contract PDF
- Risk scoring: compare extracted clauses against clause library; flag non-standard or missing clauses
- Obligation extraction: identify obligations and deadlines from contract text and suggest them as obligation records

## Data Model

```erDiagram
    legal_contracts {
        ulid id PK
        ulid company_id FK
        string title
        string contract_type
        string status
        decimal contract_value
        string currency
        date start_date
        date end_date
        boolean auto_renews
        integer renewal_notice_days
        date renewal_notice_date
        ulid owner_id FK
        json extracted_metadata
        timestamps timestamps
        softDeletes deleted_at
    }

    legal_contract_parties {
        ulid id PK
        ulid contract_id FK
        string party_type
        string party_name
        string party_role
        ulid crm_company_id FK
    }

    legal_contract_obligations {
        ulid id PK
        ulid contract_id FK
        string description
        date due_date
        ulid assigned_to FK
        boolean completed
        timestamp completed_at
    }

    legal_contracts ||--o{ legal_contract_parties : "has"
    legal_contracts ||--o{ legal_contract_obligations : "has"
```

| Table | Purpose |
|---|---|
| `legal_contracts` | Contract header with metadata and status |
| `legal_contract_parties` | Named parties linked to CRM or supplier records |
| `legal_contract_obligations` | Obligation tasks with owner and deadline |

## Permissions

```
legal.contracts.view-any
legal.contracts.create
legal.contracts.update
legal.contracts.execute
legal.contracts.delete
```

## Filament

**Resource class:** `ContractResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ContractExpiryDashboardPage` (contracts expiring by horizon), `ObligationTrackerPage`
**Widgets:** `ExpiringContractsWidget`, `ContractValueByTypeWidget`
**Nav group:** Contracts

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Ironclad | Contract lifecycle management |
| ContractPodAi | AI-assisted contract repository |
| Conga | Contract creation and management |
| Agiloft | Contract repository and workflow |

## Related

- [[e-signatures]] — contracts executed via built-in e-signature
- [[document-review]] — contracts reviewed through document workflow before execution
- [[matter-management]] — contracts attached to legal matters
- [[../crm/INDEX]] — customer contracts linked to deals
- [[../finance/INDEX]] — contract values drive revenue and cost forecasting
