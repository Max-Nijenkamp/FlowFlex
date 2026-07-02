---
domain: crm
module: customer-segments
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Customer Segments — Data Model

## crm_segments

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed; tenant scope |
| name | string | Unique per company |
| type | string | `dynamic` / `static` |
| conditions | jsonb | `{logic: and/or, rules:[{field,operator,value}]}` — dynamic only |
| member_count | int | Cached snapshot, refreshed nightly |
| deleted_at | timestamp | Nullable (soft delete) |

Indexes: `company_id`; unique `(company_id, name)`.

## crm_segment_members

Static lists only.

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| segment_id | ulid | FK → crm_segments |
| company_id | ulid | Tenant scope |
| contact_id | ulid | FK → crm_contacts |

Indexes: unique `(segment_id, contact_id)`.

> `conditions` is a JSONB document holding the AND/OR rule tree for dynamic segments. Static segments leave it null and use `crm_segment_members` instead.

## ERD

```mermaid
erDiagram
    crm_segments {
        ulid id PK
        ulid company_id
        string name
        string type
        jsonb conditions
        int member_count
        timestamp deleted_at
    }
    crm_segment_members {
        ulid id PK
        ulid segment_id FK
        ulid company_id
        ulid contact_id FK
    }
    crm_contacts {
        ulid id PK
        ulid company_id
    }
    crm_segments ||--o{ crm_segment_members : "static membership"
    crm_contacts ||--o{ crm_segment_members : "listed in"
```
