---
domain: core
module: data-import
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Data Import — Data Model

Parent: [[_module]] · See also [[architecture]] · [[api]]

Owns one table: `data_imports`.

## data_imports

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | PK | |
| company_id | ulid | not null, indexed | |
| target | string | not null | importer key, e.g. `hr.employees` |
| filename | string | not null | original name |
| status | string | not null, default `pending` | state machine |
| column_map | jsonb | not null | source column → field |
| total_rows / success_rows / error_rows | int | default 0 | |
| error_report_path | string | nullable | tenant-scoped file |
| imported_by | ulid | FK users | |
| deleted_at | timestamp | nullable | soft delete |

**Indexes:** `(company_id, created_at)`

## State column values

`status` ∈ `pending` → `processing` → (`complete` \| `failed`). Full transition table in [[architecture]].

```mermaid
erDiagram
    companies ||--o{ data_imports : "scopes"
    users ||--o{ data_imports : "imported_by"
    data_imports {
        ulid id PK
        ulid company_id FK
        string target
        string filename
        string status
        jsonb column_map
        int total_rows
        int success_rows
        int error_rows
        string error_report_path
        ulid imported_by FK
    }
```
