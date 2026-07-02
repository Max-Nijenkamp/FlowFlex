---
domain: crm
module: revenue-intelligence
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Revenue Intelligence — Data Model

## crm_deal_health

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK. |
| company_id | ulid | Indexed, tenant scope. |
| deal_id | ulid | FK, unique — one row per open deal. |
| score | int | 0–100. |
| factors | jsonb | `[{factor, score, weight, detail}]` for explainability. |
| calculated_at | timestamp | |

## crm_win_loss

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK. |
| company_id | ulid | Indexed, tenant scope. |
| deal_id | ulid | FK, unique. |
| outcome | string | won / lost. |
| reason | string | From the deal close flow. |
| competitor | string | Nullable. |
| notes | text | Nullable. |

This module also **reads** `crm_deals` and `crm_activities` as scoring / analysis inputs but does not own them.

## ERD

```mermaid
erDiagram
    crm_deals ||--o| crm_deal_health : "scored by"
    crm_deals ||--o| crm_win_loss : "closed into"
    crm_activities }o--|| crm_deals : "inform score"
    crm_deal_health {
        ulid id PK
        ulid company_id
        ulid deal_id FK
        int score
        jsonb factors
        timestamp calculated_at
    }
    crm_win_loss {
        ulid id PK
        ulid company_id
        ulid deal_id FK
        string outcome
        string reason
        string competitor
        text notes
    }
```
