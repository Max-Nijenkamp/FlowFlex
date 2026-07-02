---
domain: marketing
module: utm-tracking
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# UTM Tracking — Data Model

Owns one table. `contact_id` references CRM (read-only target); touches purge with contact erasure.

### mkt_utm_touches

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), contact_id FK | ulid | |
| touch_type | string | first / last — first immutable, last upserted |
| source / medium / campaign / term / content | string nullable | UTM params |
| landing_url | string | |
| occurred_at | timestamp | |

**Unique** `(contact_id, touch_type)` — at most one first + one last per contact.

## ERD

```mermaid
erDiagram
    CRM_CONTACTS ||--o{ MKT_UTM_TOUCHES : "attributed (read-only ref)"
    MKT_UTM_TOUCHES {
        ulid id PK
        ulid company_id
        ulid contact_id
        string touch_type
        string source
        string medium
        string campaign
        string landing_url
        timestamp occurred_at
    }
```

Attribution joins touches → CRM contacts → CRM deals (read-only) for revenue by channel.

## Related

- [[_module]] · [[architecture]] · [[security]]
