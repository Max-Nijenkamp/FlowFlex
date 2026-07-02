---
domain: core
module: notifications
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Notifications — Data Model

Parent: [[_module]] · See also [[architecture]] · [[security]]

Owns two tables: `notifications` (Laravel standard, extended) and `notification_preferences`.

## notifications

| Column | Type | Notes |
|---|---|---|
| id | uuid | PK (framework convention) |
| notifiable_type / notifiable_id | string / ulid | target user |
| type | string | notification class |
| data | jsonb | title, body, action_url, domain |
| read_at | timestamp | nullable |
| company_id | ulid | indexed — added column |
| created_at | timestamp | |

## notification_preferences

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | indexed |
| user_id | ulid | FK users |
| notification_type | string | class or type key |
| in_app_enabled | boolean | default true |
| email_enabled | boolean | default true |

**Index:** `(user_id, notification_type)` unique

```mermaid
erDiagram
    companies ||--o{ notifications : "scopes"
    users ||--o{ notifications : "notifiable"
    users ||--o{ notification_preferences : "sets"
    companies ||--o{ notification_preferences : "scopes"
```
