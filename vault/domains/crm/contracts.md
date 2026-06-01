---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.contracts
status: planned
color: "#4ADE80"
---

# Contracts

Customer contracts with renewal tracking, value, and e-signature. Sales-focused contract lifecycle (distinct from Legal's full legal contract management).

## Core Features

- Contract record: customer, deal, title, value, start/end dates, renewal terms, status
- Status machine: `draft → sent → signed → active → expired | terminated`
- Generated from a won deal or accepted quote
- E-signature integration status (DocuSign/native)
- Renewal tracking: renewal date, auto-renew flag, notice period
- Renewal alerts before expiry
- Contract value → recurring revenue tracking
- Document storage (signed PDF)
- Linked to CRM account

## Data Model

| Table | Key Columns |
|---|---|
| `crm_contracts` | company_id, account_id, deal_id, title, value_cents, start_date, end_date, renewal_date, auto_renew, status, signed_at |

## Filament

**Nav group:** Pipeline

- `ContractResource` — list, create (from deal), send for signature, view
- `ContractRenewalWidget` — upcoming renewals

## Cross-Domain

- Created from won deals; renewal revenue → Finance
- For legal/compliance contract depth, see [[domains/legal/legal-contracts]]

## Related

- [[domains/crm/deals]]
- [[domains/legal/legal-contracts]]
- `spatie/laravel-pdf`
