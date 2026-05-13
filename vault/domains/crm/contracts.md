---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.contracts
status: planned
color: "#4ADE80"
---

# Contracts

> Customer contracts — contract value, start and renewal dates, e-signature integration, renewal alerts, and contract document storage.

**Panel:** `crm`
**Module key:** `crm.contracts`

## What It Does

Contracts manages the lifecycle of executed customer contracts. A contract is typically created after a quote is accepted. It records the contract value, effective date, renewal date, auto-renewal setting, and the signed contract document. Renewal alerts notify the account manager 90, 60, and 30 days before the contract renewal date — giving time to negotiate or upsell. Contracts feed into Revenue Intelligence for churn risk analysis. The Finance team uses contract value to validate invoicing amounts.

## Features

### Core
- Contract record: contact, company, deal (origin), title, effective date, renewal date, contract value, currency, status, auto-renewal flag
- Document storage: signed contract PDF uploaded or linked from Quotes module — stored via file-storage module
- Contract status: draft / active / expired / cancelled / pending_renewal
- Renewal timeline: contracts list sortable by renewal date — upcoming renewals highlighted
- Renewal alerts: notifications sent to contract owner at 90, 60, and 30 days before renewal date

### Advanced
- E-signature: send contract for signing via built-in signature widget or DocuSign integration — `signed_at` timestamp recorded
- Contract versioning: amendments stored as new contract versions — full history retained
- Auto-renewal: if `auto_renewal = true`, contract status transitions to `pending_renewal` 90 days before expiry and owner is prompted to confirm renewal or begin renegotiation
- Contract analytics: total contract value (TCV), annual contract value (ACV), renewal rate — surfaced in Revenue Intelligence
- Parent-child contracts: master service agreement (MSA) with child order forms — linked hierarchy

### AI-Powered
- Churn risk scoring: AI analyses contract engagement signals (email activity decline, support ticket volume increase, NPS data) and flags high-churn-risk contracts 90 days before renewal
- Renewal probability: AI predicts the probability of contract renewal based on historical renewal patterns, customer health score, and usage data

## Data Model

```erDiagram
    crm_contracts {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        ulid crm_company_id FK
        ulid deal_id FK
        string title
        date effective_date
        date renewal_date
        decimal value
        string currency
        string status
        boolean auto_renewal
        string document_path
        timestamp signed_at
        integer version
        ulid parent_contract_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | draft / active / expired / cancelled / pending_renewal |
| `auto_renewal` | True triggers pending_renewal alert 90 days before expiry |
| `parent_contract_id` | Self-referential FK for MSA → Order Form hierarchy |

## Permissions

- `crm.contracts.view`
- `crm.contracts.create`
- `crm.contracts.edit`
- `crm.contracts.sign`
- `crm.contracts.manage-renewals`

## Filament

- **Resource:** `ContractResource`
- **Pages:** `ListContracts`, `CreateContract`, `ViewContract` (with version history, renewal timeline)
- **Custom pages:** `UpcomingRenewalsPage` — sorted list of contracts due for renewal in 90 days
- **Widgets:** `ContractRenewalWidget` — count of contracts due for renewal in next 30 days on CRM dashboard
- **Nav group:** Pipeline (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| DocuSign CLM | Contract lifecycle management |
| PandaDoc | Contract creation and e-signature |
| Ironclad | Contract management platform |
| Salesforce Contract Management | CRM-native contracts |

## Related

- [[deals]]
- [[quotes]]
- [[contacts]]
- [[revenue-intelligence]]
