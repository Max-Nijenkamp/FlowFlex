---
domain: events
module: sponsors
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sponsors — Data Model

## `ev_sponsors`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `event_id` | ulid | FK → `ev_events` |
| `name` | string | |
| `logo_media_id` | ulid nullable | Media Library |
| `tier` | string | platinum / gold / silver / bronze (in set) |
| `contact_id` | ulid nullable | CRM contact reference (read) |
| `amount_cents` | bigint | Sponsorship value |
| `currency` | string(3) | |
| `status` | string | committed / paid |
| `fin_invoice_id` | ulid nullable | Finance invoice reference (set by soft bridge) |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `ev_sponsor_deliverables`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `sponsor_id` | ulid | FK → `ev_sponsors` |
| `description` | string | |
| `status` | string | open / done |
| `due_date` | date nullable | |
| `reminded` | boolean | default false — idempotent reminder guard |

## ERD

```mermaid
erDiagram
    ev_events ||--o{ ev_sponsors : "sponsored by"
    ev_sponsors ||--o{ ev_sponsor_deliverables : "owes"
    ev_sponsors }o--o| crm_contacts : "contact (read)"
    ev_sponsors }o--o| fin_invoices : "invoiced (soft)"

    ev_sponsors {
        ulid id PK
        ulid event_id FK
        string name
        ulid logo_media_id
        string tier
        ulid contact_id
        bigint amount_cents
        string status
        ulid fin_invoice_id
    }
    ev_sponsor_deliverables {
        ulid id PK
        ulid sponsor_id FK
        string description
        string status
        date due_date
        boolean reminded
    }
    crm_contacts { ulid id PK }
    fin_invoices { ulid id PK }
```

> `crm_contacts` (CRM) and `fin_invoices` (Finance) are owned elsewhere; `contact_id` / `fin_invoice_id` are read/soft references. See [[../../../security/data-ownership]].
