---
type: module
domain: Document Management
panel: dms
module-key: dms.wiki
status: planned
color: "#4ADE80"
---

# Wiki Pages

Internal knowledge wiki with rich text, nested pages, and table of contents. Company handbook, SOPs, process documentation.

## Core Features

- Wiki page: title, slug, body (rich text), parent page (nested hierarchy), author
- Rich text editing via `awcodes/filament-tiptap-editor`
- Nested page tree (parent_page_id)
- Auto table of contents from headings
- Internal page linking (`[[page]]`-style or page picker)
- Slugs via `spatie/laravel-sluggable`
- Full-text search (Meilisearch)
- Page history/versions
- Access control: public to all company users or restricted
- Favourite pages

## Data Model

| Table | Key Columns |
|---|---|
| `dms_wiki_pages` | company_id, title, slug, body, parent_page_id, author_id, access_level, updated_by |
| `dms_wiki_page_versions` | page_id, company_id, body, edited_by, edited_at |

## Filament

**Nav group:** Wiki

- `WikiPageResource` — list/tree, create, edit (Tiptap)
- `WikiPage` (custom page) — rendered page view with TOC sidebar + nested page nav

## Related

- [[domains/dms/document-library]]
- [[architecture/search]]
- [[architecture/packages]] (awcodes/filament-tiptap-editor)
