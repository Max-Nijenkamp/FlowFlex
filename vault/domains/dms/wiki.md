---
type: module
domain: Document Management
domain-key: dms
panel: dms
module-key: dms.wiki
status: planned
priority: p2
depends-on: [core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages, search]
tables: [dms_wiki_pages, dms_wiki_page_versions]
permission-prefix: dms.wiki
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Wiki Pages

Internal knowledge wiki with rich text, nested pages, and table of contents. Company handbook, SOPs, process documentation.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

(Standalone within DMS — no dependency on the document library.)

---

## Core Features

- Wiki page: title, slug, body (rich text), parent page (nested hierarchy, cycle-checked), author
- Rich text editing via `awcodes/filament-tiptap-editor` — purified before storage
- Nested page tree (parent_page_id)
- Auto table of contents from headings (client-side from rendered body *(assumed)*)
- Internal page linking via page picker (stored as page-id links — survive renames *(assumed)*)
- Slugs via `spatie/laravel-sluggable`
- Full-text search (Meilisearch)
- Page history/versions (snapshot per save, capped 50 *(assumed)*)
- Access control: public to all company users or restricted (role/user list)
- Favourite pages

---

## Data Model

### dms_wiki_pages

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| slug | string | unique per company |
| body | text | purified |
| parent_page_id | ulid nullable FK self | cycle-checked |
| author_id / updated_by | ulid FK users | |
| access_level | string default `all` | all / restricted |
| access_list | jsonb nullable | role/user ids |
| deleted_at | timestamp nullable | |

### dms_wiki_page_versions — id, page_id FK, company_id, body, edited_by, edited_at (append-only, capped)

---

## DTOs

### CreateWikiPageData — title (required, max:255), body (required, purified), parent_page_id? (no cycle), access_level + access_list (required_if restricted)

## Services & Actions

- `WikiService::save(CreateWikiPageData|UpdateWikiPageData $data): WikiPageData` — version snapshot on update
- `WikiService::restoreVersion(string $versionId): WikiPageData`
- `accessiblePagesFor(User $user): Builder` — restricted pages invisible in tree + search + direct URL
- `WikiService::tree(): array` — nested nav

---

## Filament

**Nav group:** Wiki

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WikiPageResource` | #1 CRUD resource | tree-ordered list, Tiptap editor, version history relation |
| `WikiViewerPage` | #2-style custom page | rendered page + TOC sidebar + nested nav |

---

## Permissions

`dms.wiki.view-any` · `dms.wiki.create` · `dms.wiki.update` · `dms.wiki.delete` · `dms.wiki.manage-access`

---

## Search & Realtime

Meilisearch: title, body (stripped) — results filtered by access. No realtime.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Restricted page invisible (tree, search, direct URL) to non-permitted user
- [ ] Body purified (XSS fixture)
- [ ] Version snapshot per save; restore works; cap enforced
- [ ] Page tree cycle rejected
- [ ] Page-id links survive rename

---

## Build Manifest

```
database/migrations/xxxx_create_dms_wiki_pages_table.php
database/migrations/xxxx_create_dms_wiki_page_versions_table.php
app/Models/DMS/{WikiPage,WikiPageVersion}.php
app/Data/DMS/{CreateWikiPageData,WikiPageData}.php
app/Services/DMS/WikiService.php
app/Filament/DMS/Resources/WikiPageResource.php
app/Filament/DMS/Pages/WikiViewerPage.php
database/factories/DMS/WikiPageFactory.php
tests/Feature/DMS/{WikiPageTest,WikiAccessTest}.php
```

---

## Related

- [[domains/dms/document-library]]
- [[architecture/search]]
- [[architecture/packages]] (awcodes/filament-tiptap-editor)
