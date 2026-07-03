---
domain: crm
module: leads
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leads — Data Model

> Source spec described the data model as prose, not a table; it has been normalised into the table below. See [[unknowns]].

## `crm_leads`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string | Lead / person name |
| `company_name` | string | Prospect company |
| `email` | string | Used for contact matching on convert |
| `phone` | string | E.164 via `propaganistas/laravel-phone` (decision 2026-07-03). |
| `source` | string | manual / website / referral / event / import |
| `status` | string | new / working / qualified / converted / unqualified |
| `owner_id` | ulid | FK → `users` |
| `estimated_value_cents` | bigint | Minor currency unit |
| `notes` | text | |
| `converted_deal_id` | ulid nullable | FK → `crm_deals` |
| `converted_at` | timestamp nullable | Set on convert |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Indexes:** `(company_id, status)`.

## ERD

```mermaid
erDiagram
    crm_leads ||--o| crm_deals : "converts to"
    crm_leads }o--o| crm_contacts : "matches/creates by email"
    users ||--o{ crm_leads : owns

    crm_leads {
        ulid id PK
        ulid company_id
        string name
        string company_name
        string email
        string phone
        string source
        string status
        ulid owner_id FK
        bigint estimated_value_cents
        text notes
        ulid converted_deal_id FK
        timestamp converted_at
        timestamp deleted_at
    }
    crm_deals {
        ulid id PK
        ulid company_id
    }
    crm_contacts {
        ulid id PK
        ulid company_id
        string email
    }
```

> **PII decision (2026-07-03):** lead `email`/`phone` stay **plaintext** (matching `crm.contacts` — search/dedupe need them queryable); covered by the CRM retention rules in [[../../../architecture/data-lifecycle]]. Phone always normalized to E.164 on the DTO.
