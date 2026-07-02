---
domain: events
module: speakers
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Speakers — Data Model

## `ev_speakers`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `name` | string | |
| `bio` | text | HTMLPurifier-sanitized |
| `photo_media_id` | ulid nullable | Media Library |
| `title` | string nullable | |
| `company_name` | string nullable | |
| `social_links` | jsonb | |
| `logistics` | jsonb | Internal — never public |
| `submit_token` | uuid | Unique — signed self-submit link |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `ev_session_speakers`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `session_id` | ulid | FK → `ev_sessions` |
| `speaker_id` | ulid | FK → `ev_speakers` |
| `confirmation_status` | string | invited / confirmed / declined |

**Indexes:** unique `(session_id, speaker_id)`.

## ERD

```mermaid
erDiagram
    ev_speakers ||--o{ ev_session_speakers : "assigned"
    ev_sessions ||--o{ ev_session_speakers : "features"

    ev_speakers {
        ulid id PK
        ulid company_id
        string name
        text bio
        ulid photo_media_id
        string title
        string company_name
        jsonb social_links
        jsonb logistics
        uuid submit_token
        timestamp deleted_at
    }
    ev_session_speakers {
        ulid id PK
        ulid session_id FK
        ulid speaker_id FK
        string confirmation_status
    }
    ev_sessions { ulid id PK }
```

> `ev_sessions` is owned by [[../events/_module|events.events]]; shown for the FK only.
