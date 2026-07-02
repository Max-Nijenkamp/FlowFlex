---
domain: legal
module: legal-spend
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Spend — Data Model

## legal_expenses

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), matter_id FK | ulid | |
| vendor | string | law firm |
| amount_cents | bigint > 0 | brick/money |
| currency | string(3) | |
| expense_date | date | ≤ today |
| category | string | counsel / court / filing / other *(assumed)* |
| invoice_reference | string nullable | unique `(company_id, vendor, invoice_reference)` |
| status | string default `pending` | pending / approved / rejected |
| approved_by | ulid nullable FK users | ≠ submitter |
| fin_bill_id | ulid nullable | finance.ap link (reference only) |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, matter_id, status)`, `(company_id, vendor)`

---

## legal_budgets

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| matter_id | ulid nullable | null = period budget |
| period | string | e.g. `2026-Q3` |
| budget_cents | bigint | |

Unique `(company_id, matter_id, period)`.

---

## ERD

```mermaid
erDiagram
    legal_expenses {
        ulid id PK
        ulid company_id FK
        ulid matter_id FK
        string vendor
        bigint amount_cents
        string currency
        date expense_date
        string category
        string invoice_reference
        string status
        ulid approved_by FK
        ulid fin_bill_id
    }
    legal_budgets {
        ulid id PK
        ulid company_id FK
        ulid matter_id FK
        string period
        bigint budget_cents
    }
    legal_budgets ||--o{ legal_expenses : "budget covers (by matter/period)"
```

`matter_id` references `legal_matters` (owned by [[../matter-management/_module|legal.matters]]) — read-only.
