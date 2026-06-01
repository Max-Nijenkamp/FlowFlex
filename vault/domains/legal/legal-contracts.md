---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.contracts
status: planned
color: "#4ADE80"
---

# Legal Contracts

Central contract repository with key dates, renewal tracking, obligations, and e-signature status. Different from CRM contracts (those are sales-focused; these are legal-focused with full lifecycle).

## Core Features

- Contract record: title, counterparty, type, value, start/end dates, renewal terms, status
- Contract types: NDA, MSA, vendor, employment, lease, partnership
- Status machine: `draft → in_review → signed → active → expired | terminated`
- Key date tracking: renewal date, notice period deadline, expiry
- Renewal alerts: notify before notice deadline
- Obligations tracking: deliverables, payment milestones with due dates
- Document storage (signed PDF via Media Library)
- E-signature integration status
- Linked to CRM account or supplier

## Data Model

| Table | Key Columns |
|---|---|
| `legal_contracts` | company_id, title, counterparty, type, value_cents, start_date, end_date, renewal_date, notice_period_days, status, owner_id |
| `legal_contract_obligations` | contract_id, company_id, description, due_date, status, responsible_id |

## Filament

**Nav group:** Contracts

- `ContractResource` — list (filter by type/status/renewal), create, edit, view
- `ContractRenewalWidget` — upcoming renewals + notice deadlines
- Obligations tracked as relation manager

## Cross-Domain

- Linked to CRM accounts and Operations suppliers

## Related

- [[domains/legal/matter-management]]
- [[domains/crm/contracts]]
- [[domains/dms/document-library]]
