---
domain: legal
module: legal-contracts
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Contracts — Data Model

## legal_contracts

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| counterparty | string | + crm_account_id / ops_supplier_id nullable links |
| type | string | in set: NDA / MSA / vendor / employment / lease / partnership |
| value_cents | bigint nullable | brick/money |
| currency | string(3) | |
| start_date / end_date | date | end after start |
| renewal_date | date nullable | |
| notice_period_days | int default 30 | |
| status | string default `draft` | state machine |
| owner_id | ulid FK users | |
| matter_id | ulid nullable | legal.matters link |
| alerted_levels | jsonb default `[]` | 90/30 once-guards |
| signed_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status, renewal_date)`, `(company_id, type)`

---

## legal_contract_obligations

| Column | Type | Notes |
|---|---|---|
| id, contract_id FK, company_id (indexed) | ulid | |
| description | string | deliverable / payment milestone |
| due_date | date | |
| status | string | open / done / overdue |
| responsible_id | ulid FK users | |
| alerted | boolean default false | overdue once-guard |

---

## ERD

```mermaid
erDiagram
    legal_contracts {
        ulid id PK
        ulid company_id FK
        string title
        string counterparty
        string type
        bigint value_cents
        string currency
        date start_date
        date end_date
        date renewal_date
        int notice_period_days
        string status
        ulid owner_id FK
        ulid matter_id FK
        jsonb alerted_levels
        timestamp signed_at
    }
    legal_contract_obligations {
        ulid id PK
        ulid contract_id FK
        ulid company_id FK
        string description
        date due_date
        string status
        ulid responsible_id FK
        boolean alerted
    }
    legal_contracts ||--o{ legal_contract_obligations : "has obligations"
```
