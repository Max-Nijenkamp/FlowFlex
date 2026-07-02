---
domain: dms
module: document-library
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Library — Data Model

## `dms_folders`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string | unique `(parent_folder_id, name)` *(assumed)* |
| `parent_folder_id` | ulid nullable | FK self, cycle-checked |
| `owner_id` | ulid | FK → `users` |
| `access_level` | string | `all` / `restricted`, default `all` |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `dms_folder_access`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `folder_id` | ulid | FK → `dms_folders` |
| `role_id` | ulid nullable | Exactly one of role/user set |
| `user_id` | ulid nullable | Exactly one of role/user set |

## `dms_documents`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `folder_id` | ulid | FK → `dms_folders` |
| `name` | string | |
| `slug` | string | `spatie/laravel-sluggable`, unique per company |
| `description` | text nullable | |
| `owner_id` | ulid | FK → `users` |
| `file_size` | bigint | bytes |
| `mime_type` | string | |
| `extracted_text` | text nullable | Meilisearch source |
| `is_archived` | boolean | default false; set by [[../retention-policies/_module\|retention]] |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Indexes:** `(company_id, folder_id)`, `(company_id, owner_id)`.

## `dms_favourites`

*(assumed)* — `id`, `company_id`, `user_id` FK, `document_id` FK; unique `(user_id, document_id)`.

## ERD

```mermaid
erDiagram
    dms_folders ||--o{ dms_folders : "parent of"
    dms_folders ||--o{ dms_folder_access : restricts
    dms_folders ||--o{ dms_documents : contains
    users ||--o{ dms_documents : owns
    dms_documents ||--o{ dms_favourites : starred
    users ||--o{ dms_favourites : stars

    dms_folders {
        ulid id PK
        ulid company_id
        string name
        ulid parent_folder_id FK
        ulid owner_id FK
        string access_level
        timestamp deleted_at
    }
    dms_folder_access {
        ulid id PK
        ulid company_id
        ulid folder_id FK
        ulid role_id FK
        ulid user_id FK
    }
    dms_documents {
        ulid id PK
        ulid company_id
        ulid folder_id FK
        string name
        string slug
        ulid owner_id FK
        bigint file_size
        string mime_type
        text extracted_text
        boolean is_archived
        timestamp deleted_at
    }
    dms_favourites {
        ulid id PK
        ulid company_id
        ulid user_id FK
        ulid document_id FK
    }
```

The media/file record itself is owned by [[../../core/file-storage/_module|core.files]] (Media Library), referenced by `dms_documents`, never duplicated here.
