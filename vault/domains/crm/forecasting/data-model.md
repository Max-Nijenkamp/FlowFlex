---
domain: crm
module: forecasting
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Data Model

This module owns `crm_quotas` and `crm_forecast_snapshots`, and reads deal data from `crm_deals` (adding a `forecast_category` column to it).

## crm_quotas

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed, tenant scope |
| owner_id | FK users | Rep; team roll-up computed |
| period | string | `YYYY-MM` or `YYYY-Qn` |
| quota_cents | bigint | Target amount (minor unit) |
| currency | string(3) | ISO currency |

**Indexes:** `company_id`; unique `(company_id, owner_id, period)`.

## crm_forecast_snapshots

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed, tenant scope |
| owner_id | FK users | Rep |
| period | string | `YYYY-MM` or `YYYY-Qn` |
| category | string | commit / best-case / pipeline / closed |
| amount_cents | bigint | Snapshot amount (minor unit) |
| captured_at | timestamp | Weekly snapshot job |

**Indexes:** `company_id`; upsert key `(company_id, owner_id, period, category, captured_at week)`.

## crm_deals (read + augmented)

This module adds a `forecast_category` column (nullable enum: commit / best-case / pipeline / closed) to the `crm_deals` table owned by [[../deals/_module|Deals]]. Deal value, probability, stage and close dates are read for weighted-pipeline computation.

## ER Diagram

```mermaid
erDiagram
    crm_quotas {
        ulid id PK
        ulid company_id
        ulid owner_id FK
        string period
        bigint quota_cents
        string currency
    }
    crm_forecast_snapshots {
        ulid id PK
        ulid company_id
        ulid owner_id FK
        string period
        string category
        bigint amount_cents
        timestamp captured_at
    }
    crm_deals {
        ulid id PK
        ulid company_id
        ulid owner_id FK
        bigint value_cents
        int probability
        string forecast_category
    }
    users {
        ulid id PK
    }
    users ||--o{ crm_quotas : "owns"
    users ||--o{ crm_forecast_snapshots : "owns"
    users ||--o{ crm_deals : "owns"
```
