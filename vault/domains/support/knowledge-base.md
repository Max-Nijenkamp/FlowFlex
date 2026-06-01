---
type: module
domain: Support & Help Desk
panel: support
module-key: support.kb
status: planned
color: "#4ADE80"
---

# Knowledge Base

Self-service article library. Internal agents reference articles; customers browse a public help centre. Reduces ticket volume by deflecting common questions.

## Core Features

- Article record: title, slug, body (rich text), category, status (draft/published), author
- Categories and sub-categories for organisation
- Rich text editing via `awcodes/filament-tiptap-editor`
- Slugs via `spatie/laravel-sluggable`
- Public help centre: published articles browsable at a public URL (Vue + Inertia)
- Article search via Meilisearch
- Article feedback: "Was this helpful?" thumbs up/down with counts
- View count tracking per article
- Suggest relevant articles to agents while replying to a ticket
- Article versioning (track edits)

## Data Model

| Table | Key Columns |
|---|---|
| `sup_kb_articles` | company_id, title, slug, body, category_id, status, author_id, view_count, helpful_count, not_helpful_count, published_at |
| `sup_kb_categories` | company_id, name, slug, parent_category_id, order |

## Filament

**Nav group:** Knowledge Base

- `KbArticleResource` — list, create, edit (Tiptap editor), publish/unpublish
- `KbCategoryResource` — manage category tree

## Public Frontend

Help centre pages in Vue + Inertia (see [[frontend/_index]]):
- `/help` — category browse
- `/help/{category}/{slug}` — article detail

## Related

- [[domains/support/tickets]]
- [[architecture/search]]
- [[frontend/_index]]
