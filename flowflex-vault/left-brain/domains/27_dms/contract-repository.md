---
type: module
domain: Document Management
panel: dms
phase: 4
status: complete
cssclasses: domain-dms
migration_range: 996500–996999
last_updated: 2026-05-12
---

# Contract Repository

Centralised, searchable store for all signed contracts. Expiry alerts, obligation tracking, and renewal workflows. Never miss a contract renewal or notice period again.

---

## What Gets Stored

All executed (signed) contracts across the business:
- Customer contracts (MSA, SLA, SOW — from CRM/PSA)
- Supplier contracts (from Procurement)
- Employment contracts (from HR)
- Lease agreements (from Real Estate)
- Partnership/reseller agreements
- Software licence agreements
- Insurance policies

---

## Contract Record

Each contract:
- Signed PDF (immutable)
- Contract type + category
- Counterparty (linked to CRM contact or supplier record)
- Key dates: signed date, effective date, expiry date, notice period
- Value: total contract value, annual value, currency
- Renewal type: auto-renew / manual renew / expires
- Owner (internal contact responsible for renewal)
- Key obligations (custom notes or structured fields)
- Tags and custom fields

---

## Expiry & Renewal Alerts

Configurable reminders per contract type:
- 90 days before expiry → owner notified
- 60 days → escalate if no action taken
- 30 days → escalate to owner's manager
- Day of expiry → urgent alert

Actions available:
- Renew: trigger renewal workflow (new SOW, price negotiation, re-sign)
- Terminate: log decision not to renew, notify counterparty
- Extend: amend expiry date (requires [[e-signature]])

---

## Search & Filter

Full-text search across contract titles, counterparty names, and extracted text (OCR of uploaded PDFs).

Filters:
- Contract type, status, owner
- Expiring in next 30/60/90 days
- By counterparty (see all contracts with Supplier X)
- By value band

---

## Obligation Tracker

Key obligations within contracts can be structured:
- Delivery milestones
- Payment schedule
- SLA obligations
- Reporting requirements
- Renewal notice deadlines

Each obligation has an owner + due date + reminder. Links to task/project management.

---

## Data Model

### `dms_contracts`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| document_id | ulid | FK |
| title | varchar(300) | |
| contract_type | varchar(100) | |
| counterparty_type | enum | contact/supplier/employee/other |
| counterparty_id | ulid | nullable FK |
| signed_date | date | nullable |
| effective_date | date | nullable |
| expiry_date | date | nullable |
| notice_period_days | int | nullable |
| renewal_type | enum | auto/manual/expires |
| total_value | decimal(14,2) | nullable |
| currency | char(3) | nullable |
| owner_id | ulid | FK employees |
| status | enum | active/expired/terminated/draft |

### `dms_contract_obligations`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| contract_id | ulid | FK |
| description | text | |
| due_date | date | nullable |
| owner_id | ulid | FK |
| status | enum | pending/completed/overdue |

---

## Migration

```
996500_create_dms_contracts_table
996501_create_dms_contract_obligations_table
```

---

## Related

- [[MOC_DMS]]
- [[e-signature]]
- [[document-workflows]]
- [[version-control]]
- [[MOC_Procurement]] — supplier contracts
- [[MOC_CRM]] — customer contracts
