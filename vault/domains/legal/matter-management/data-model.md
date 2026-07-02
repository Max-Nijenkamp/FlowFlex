---
domain: legal
module: matter-management
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Matter Management — Data Model

## legal_matters

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| type | string | in set: litigation / advisory / dispute / IP |
| status | string default `open` | state machine |
| owner_id | ulid FK users | |
| external_counsel | string nullable | law firm |
| priority / risk_level | string | low / medium / high |
| is_confidential | boolean default false | |
| access_list | jsonb nullable | user ids when confidential |
| opened_at / closed_at | timestamp | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status)`, `(company_id, owner_id)`

---

## legal_matter_events

| Column | Type | Notes |
|---|---|---|
| id, matter_id FK, company_id (indexed) | ulid | |
| title | string | |
| event_date | date | |
| is_deadline | boolean | deadline events alert 7d before *(assumed)* |
| alerted | boolean default false | 7d once-guard |
| notes | text nullable | |
| created_by | ulid FK users | |

---

## ERD

```mermaid
erDiagram
    legal_matters {
        ulid id PK
        ulid company_id FK
        string title
        string type
        string status
        ulid owner_id FK
        string external_counsel
        string priority
        string risk_level
        boolean is_confidential
        jsonb access_list
        timestamp opened_at
        timestamp closed_at
    }
    legal_matter_events {
        ulid id PK
        ulid matter_id FK
        ulid company_id FK
        string title
        date event_date
        boolean is_deadline
        boolean alerted
        text notes
        ulid created_by FK
    }
    legal_matters ||--o{ legal_matter_events : "timeline"
```
