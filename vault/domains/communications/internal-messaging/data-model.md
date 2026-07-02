---
domain: communications
module: internal-messaging
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Internal Messaging — Data Model

## `comms_channels_internal`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string nullable | null for DMs |
| `description` | text nullable | |
| `type` | string | dm / public / private |
| `dm_key` | string nullable | unique — sorted user-id pair hash (DM dedupe) |
| `created_by` | ulid | FK → `users` |
| `deleted_at` | timestamp nullable | Soft delete |

## `comms_channel_members`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `channel_id` | ulid | FK → `comms_channels_internal` |
| `company_id` | ulid | Indexed |
| `user_id` | ulid | FK → `users` |
| `last_read_at` | timestamp nullable | unread cursor |

Unique `(channel_id, user_id)`.

## `comms_internal_messages`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `channel_id` | ulid | FK → `comms_channels_internal` |
| `company_id` | ulid | Indexed |
| `user_id` | ulid | FK → `users` (author) |
| `body` | text | purified, max 4000 |
| `parent_message_id` | ulid nullable | FK self — threads |
| `reactions` | jsonb | default `{}` — `{emoji: [user_ids]}` |
| `deleted_at` | timestamp nullable | Soft delete |

**Indexes:** `(channel_id, created_at)` (cursor-paginated feed).

## ERD

```mermaid
erDiagram
    comms_channels_internal ||--o{ comms_channel_members : "has"
    comms_channels_internal ||--o{ comms_internal_messages : "contains"
    comms_internal_messages ||--o{ comms_internal_messages : "threads (parent)"
    users ||--o{ comms_channel_members : "member of"
    comms_channels_internal {
        ulid id PK
        ulid company_id
        string name
        text description
        string type
        string dm_key
        ulid created_by FK
        timestamp deleted_at
    }
    comms_channel_members {
        ulid id PK
        ulid channel_id FK
        ulid company_id
        ulid user_id FK
        timestamp last_read_at
    }
    comms_internal_messages {
        ulid id PK
        ulid channel_id FK
        ulid company_id
        ulid user_id FK
        text body
        ulid parent_message_id FK
        jsonb reactions
        timestamp deleted_at
    }
```

## Related

- [[_module]] · [[architecture]]
