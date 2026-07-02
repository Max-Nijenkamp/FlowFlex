---
domain: marketing
module: landing-pages
type: data-model
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Landing Pages — Data Model

Owns one table. Block content is a typed JSON array validated against `BlockRegistry`.

### mkt_landing_pages

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| slug | string | sluggable, unique per company |
| blocks | jsonb | `[{type, config}]` — types in registry |
| meta_title / meta_description / og_image | string nullable | SEO |
| status | string default `draft` | draft / published |
| published_at | timestamp nullable | |
| visit_count | int default 0 | |
| deleted_at | timestamp nullable | |

## ERD

```mermaid
erDiagram
    MKT_LANDING_PAGES {
        ulid id PK
        ulid company_id
        string name
        string slug
        jsonb blocks
        string status
        timestamp published_at
        int visit_count
    }
    MKT_FORMS ||..o{ MKT_LANDING_PAGES : "embedded (read-only ref in block config)"
```

Form-block config holds a `form_id` reference read from [[../forms/_module|Forms]] — no FK write into forms.

## Related

- [[_module]] · [[architecture]] · [[security]]
