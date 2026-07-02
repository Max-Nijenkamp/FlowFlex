---
domain: support
module: knowledge-base
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Knowledge Base — Data Model

## sup_kb_articles

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | searchable |
| slug | string | sluggable, unique `(company_id, slug)` |
| body | text | purified rich text |
| revisions | jsonb | `[{body, author_id, saved_at}]` capped 20 |
| category_id | ulid FK | |
| status | string default `draft` | draft / published |
| author_id | ulid FK users | |
| view_count | int default 0 | |
| helpful_count | int default 0 | |
| not_helpful_count | int default 0 | |
| published_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

## sup_kb_categories

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| slug | string | |
| parent_category_id | ulid nullable | sub-categories |
| order | int | display order |
| deleted_at | timestamp nullable | |

---

## ERD

```mermaid
erDiagram
    sup_kb_articles {
        ulid id PK
        ulid company_id FK
        string title
        string slug
        text body
        jsonb revisions
        ulid category_id FK
        string status
        ulid author_id FK
        int view_count
        int helpful_count
        int not_helpful_count
    }
    sup_kb_categories {
        ulid id PK
        ulid company_id FK
        string name
        string slug
        ulid parent_category_id FK
        int order
    }
    sup_kb_categories ||--o{ sup_kb_articles : "contains"
    sup_kb_categories ||--o{ sup_kb_categories : "parent/sub"
```
