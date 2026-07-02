---
domain: dms
module: version-control
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Version Control — Data Model

## `dms_document_versions`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `document_id` | ulid | FK → `dms_documents` (owned by [[../document-library/_module\|dms.library]]) |
| `version_number` | int | Sequential; unique `(document_id, version_number)` |
| `media_id` | ulid | FK → media (owned by [[../../core/file-storage/_module\|core.files]]) |
| `uploaded_by` | ulid | FK → `users` |
| `change_note` | string nullable | Free-text note describing the change |
| `is_current` | boolean | Exactly one current per document — **partial unique** on `(document_id)` where `is_current` |
| `created_at` | timestamp | Upload date |

## `dms_document_locks`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `document_id` | ulid | FK → `dms_documents`; **unique** (one lock per document) |
| `locked_by` | ulid | FK → `users` |
| `locked_at` | timestamp | Auto-expires after 4h *(assumed)* — cleared by `ExpireStaleLocksCommand` |

## ERD

```mermaid
erDiagram
    dms_documents ||--o{ dms_document_versions : "has versions"
    dms_documents ||--o| dms_document_locks : "locked by"
    users ||--o{ dms_document_versions : uploaded
    users ||--o{ dms_document_locks : holds
    media ||--o{ dms_document_versions : "stores bytes"

    dms_document_versions {
        ulid id PK
        ulid company_id
        ulid document_id FK
        int version_number
        ulid media_id FK
        ulid uploaded_by FK
        string change_note
        boolean is_current
        timestamp created_at
    }
    dms_document_locks {
        ulid id PK
        ulid company_id
        ulid document_id FK
        ulid locked_by FK
        timestamp locked_at
    }
```

`dms_documents` (referenced by `document_id`) is owned by [[../document-library/_module|dms.library]]; the `media` record referenced by `media_id` is owned by [[../../core/file-storage/_module|core.files]] (Media Library). Neither is duplicated here — this module only owns `dms_document_versions` and `dms_document_locks`.
