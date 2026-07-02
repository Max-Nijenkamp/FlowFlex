---
domain: dms
module: templates
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Templates — Data Model

Templates owns exactly one table: `dms_templates`. Generated documents live in `dms_documents`, owned by [[../document-library/_module|dms.library]] and written only through its service.

## `dms_templates`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string | |
| `category` | string | `hr-contracts` / `legal` / `finance` / `general` |
| `body` | text | Purified rich text, `{{field}}` placeholders |
| `merge_fields` | jsonb | Declared fields + source hints |
| `is_system` | boolean | Seeded, read-only (copy-on-edit); default false |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Indexes:** `(company_id, category)` *(assumed)*.

## ERD

```mermaid
erDiagram
    dms_templates ||--o{ dms_documents : "generates (via library service)"
    hr_employees ||..o{ dms_templates : "merge source (read-only)"
    crm_contacts ||..o{ dms_templates : "merge source (read-only)"

    dms_templates {
        ulid id PK
        ulid company_id
        string name
        string category
        text body
        jsonb merge_fields
        boolean is_system
        timestamp deleted_at
    }
    dms_documents {
        ulid id PK
        ulid company_id
        ulid folder_id FK
        string name
        string mime_type
    }
```

The generated `dms_documents` row (and its media bytes) is created by `dms.library`'s `DocumentService::upload`, never written here. HR / CRM records are **read-only merge sources** resolved through their registered providers (whitelisted fields only) — no foreign key is stored ([[../../../security/data-ownership]]).
