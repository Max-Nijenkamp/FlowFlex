---
domain: crm
module: contracts
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contracts — Data Model

## crm_contracts

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK. |
| company_id | ulid | Indexed, tenant scope. |
| account_id | ulid | Not null, FK → CRM account. |
| deal_id | ulid | Nullable, FK → `crm_deals`. |
| title | string | Not null. |
| value_cents | bigint | ≥ 0. |
| currency | string(3) | ISO currency. |
| billing_interval | string | one-off / monthly / yearly *(assumed)*; drives recurring-revenue calc. |
| start_date | date | |
| end_date | date | Must be after `start_date`. |
| renewal_date | date | Nullable. |
| auto_renew | bool | Default false. |
| notice_period_days | int | Default 30. |
| status | string | Default `draft`; state machine. |
| signed_at | timestamp | Nullable. |
| alerted_levels | jsonb | Default `[]`; 90/30-day once-guards. |
| deleted_at | timestamp | Nullable, soft delete. |

### Indexes

- `company_id`
- `status`
- `renewal_date`

## ERD

```mermaid
erDiagram
    crm_accounts ||--o{ crm_contracts : "has"
    crm_deals ||--o| crm_contracts : "generates"
    crm_contracts {
        ulid id PK
        ulid company_id
        ulid account_id FK
        ulid deal_id FK
        string title
        bigint value_cents
        string currency
        string billing_interval
        date start_date
        date end_date
        date renewal_date
        bool auto_renew
        int notice_period_days
        string status
        timestamp signed_at
        jsonb alerted_levels
    }
```
