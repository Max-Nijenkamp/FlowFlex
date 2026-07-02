---
domain: dms
module: retention-policies
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Retention Policies — Data Model

Retention **owns** three tables. It acts on `dms_documents` (owned by [[../document-library/_module|dms.library]]) but never writes it directly — see [[decisions]].

## `dms_retention_policies`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string | |
| `applies_to_type` | string | `folder` / `tag` |
| `applies_to_id` | ulid nullable | folder id / tag id |
| `retention_days` | int | min 1 |
| `action` | string | `archive` / `delete` |
| `clock_from` | string | `created` / `modified` |
| `is_active` | boolean | default `true` |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `dms_legal_holds`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `document_id` | ulid | FK → `dms_documents` (owned by `dms.library`) |
| `reason` | string | **required**, max 1000 |
| `placed_by` | ulid | FK → `users` |
| `placed_at` | timestamp | |
| `released_at` | timestamp nullable | null = active; **one active hold per document** |

## `dms_retention_log`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `document_id` | ulid | the document acted on |
| `policy_id` | ulid nullable | policy that triggered the action |
| `action` | string | `archived` / `soft-deleted` / `hard-deleted` / `notified` |
| `executed_at` | timestamp | |

**Append-only** — never updated or deleted; kept as compliance proof. The `(document_id, action)` pair is the idempotency guard for `ProcessRetentionCommand` re-runs.

## ERD

```mermaid
erDiagram
    dms_documents ||--o{ dms_legal_holds : "held by"
    dms_documents ||--o{ dms_retention_log : "logged for"
    dms_retention_policies ||--o{ dms_retention_log : "triggered"
    users ||--o{ dms_legal_holds : "placed by"

    dms_retention_policies {
        ulid id PK
        ulid company_id
        string name
        string applies_to_type
        ulid applies_to_id
        int retention_days
        string action
        string clock_from
        boolean is_active
        timestamp deleted_at
    }
    dms_legal_holds {
        ulid id PK
        ulid company_id
        ulid document_id FK
        string reason
        ulid placed_by FK
        timestamp placed_at
        timestamp released_at
    }
    dms_retention_log {
        ulid id PK
        ulid company_id
        ulid document_id
        ulid policy_id FK
        string action
        timestamp executed_at
    }
    dms_documents {
        ulid id PK
        boolean is_archived
        timestamp deleted_at
    }
```

`dms_documents` is shown for context only — it is **owned by** [[../document-library/_module|dms.library]]. Retention reads it to find expired documents and commands `dms.library`'s `DocumentService` to set `is_archived` or soft/hard-delete; it never writes those columns itself ([[../../../security/data-ownership|data-ownership]]).
