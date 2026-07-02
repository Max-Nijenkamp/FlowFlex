---
domain: crm
module: deal-rooms
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deal Rooms — Data Model

## crm_deal_rooms

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK. |
| company_id | ulid | Indexed, tenant scope. |
| deal_id | ulid | FK, unique — one room per deal. |
| access_token | uuid | Unique; public link. |
| branding | jsonb | Logo / colour overrides. |
| expires_at | timestamp | Default deal close date + 30d *(assumed)*. |
| revoked_at | timestamp | Nullable. |

## crm_deal_room_documents

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK. |
| room_id | ulid | FK → `crm_deal_rooms`. |
| company_id | ulid | Tenant scope. |
| media_id | ulid | FK → media (tenant-scoped file). |
| view_count | int | Default 0. |
| last_viewed_at | timestamp | Nullable. |

## crm_deal_room_action_items

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK. |
| room_id | ulid | FK → `crm_deal_rooms`. |
| company_id | ulid | Tenant scope. |
| description | string | |
| owner_side | string | buyer / seller. |
| status | string | Default `open` (open / done). |
| due_date | date | Nullable. |

## crm_deal_room_stakeholders

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK. |
| room_id | ulid | FK → `crm_deal_rooms`. |
| company_id | ulid | Tenant scope. |
| name | string | |
| role | string | |
| contact_id | ulid | Nullable, FK → `crm_contacts`. |

## ERD

```mermaid
erDiagram
    crm_deals ||--o| crm_deal_rooms : "one room per deal"
    crm_deal_rooms ||--o{ crm_deal_room_documents : "shares"
    crm_deal_rooms ||--o{ crm_deal_room_action_items : "tracks"
    crm_deal_rooms ||--o{ crm_deal_room_stakeholders : "maps"
    crm_contacts ||--o{ crm_deal_room_stakeholders : "linked to"
    crm_deal_rooms {
        ulid id PK
        ulid company_id
        ulid deal_id FK
        uuid access_token
        jsonb branding
        timestamp expires_at
        timestamp revoked_at
    }
    crm_deal_room_documents {
        ulid id PK
        ulid room_id FK
        ulid media_id FK
        int view_count
        timestamp last_viewed_at
    }
    crm_deal_room_action_items {
        ulid id PK
        ulid room_id FK
        string owner_side
        string status
        date due_date
    }
    crm_deal_room_stakeholders {
        ulid id PK
        ulid room_id FK
        ulid contact_id FK
        string name
        string role
    }
```
