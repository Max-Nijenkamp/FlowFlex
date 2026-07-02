---
domain: legal
module: dsar-processing
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# DSAR Processing — Data Model

This module owns **one** table. The DSAR request itself (`dsar_requests`) is owned by [[../../core/data-privacy/_module|core.privacy]] — referenced by FK, never written here.

## legal_dsar_actions

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| dsar_request_id | ulid FK dsar_requests | **core.privacy table** (read reference) |
| action | string | verified / discovery-run / export-delivered / erasure-run / rectified / rejected |
| domain | string nullable | per-domain steps |
| 🔐 notes | text nullable | **encrypted cast** — may reference data-subject PII; required for rejected/rectified |
| performed_by | ulid FK users | |
| performed_at | timestamp | |

Append-only — compliance proof, never purged ([[../../../architecture/data-lifecycle]]).

---

## ERD

```mermaid
erDiagram
    dsar_requests {
        ulid id PK
        ulid company_id FK
        string type
        string status
        timestamp due_at
    }
    legal_dsar_actions {
        ulid id PK
        ulid company_id FK
        ulid dsar_request_id FK
        string action
        string domain
        text notes
        ulid performed_by FK
        timestamp performed_at
    }
    dsar_requests ||--o{ legal_dsar_actions : "action trail (append-only)"
```

`dsar_requests` shown for context only — **owned by core.privacy**, read-only here.
