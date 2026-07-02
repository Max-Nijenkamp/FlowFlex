---
domain: dms
module: wiki
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Wiki

Internal knowledge wiki with rich text, nested pages, per-page access control, version history, and a table of contents. Company handbook, SOPs, and process documentation. **Standalone within the DMS domain** — it shares no tables or edges with the document library; a wiki page is a database record, not a stored file.

## Module-key

| Field | Value |
|---|---|
| key | `dms.wiki` |
| priority | p2 |
| panel | dms |
| permission-prefix | `dms.wiki` |
| tables | `dms_wiki_pages`, `dms_wiki_page_versions` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating (`hasModule('dms.wiki')`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()`, per-page access lists |

Standalone within DMS — **no dependency on the document library**. Rich text, slugs, and search are platform reads (see Cross-Domain Edges).

## Core Features

- **Wiki page** — title, slug, body (rich text), parent page (nested hierarchy, cycle-checked), author, updater.
- **Rich text editing** via `awcodes/filament-tiptap-editor` — body **purified before storage** (`ezyang/htmlpurifier`).
- **Nested page tree** — `parent_page_id` self-referential, cycle-checked; drives the nav sidebar.
- **Auto table of contents** — built from the rendered body's headings (client-side *(assumed)*).
- **Internal page linking** — via a page picker, stored as page-id links so they survive page renames *(assumed)*.
- **Slugs** — via `spatie/laravel-sluggable`, unique per company.
- **Full-text search** — Meilisearch over title + stripped body; results **post-filtered by page access**.
- **Page history / versions** — snapshot per save, restore to any snapshot; append-only, capped 50 *(assumed)*.
- **Access control** — `all` (every company user) or `restricted` (role/user list); restricted pages invisible in tree, search, AND direct URL.
- **Favourite pages** — per-user starring.

## See features/

- [[features/page-editor|Page Editor]] — Tiptap create/edit form + version snapshot on save.
- [[features/page-tree|Page Tree]] — nested hierarchy + nav sidebar (cycle-checked parenting).
- [[features/wiki-viewer|Wiki Viewer]] — rendered page + auto-TOC + nested nav custom page.
- [[features/page-history|Page History]] — version snapshots + restore.
- [[features/wiki-search|Wiki Search]] — access-filtered Meilisearch.
- [[features/page-access-control|Page Access Control]] — `all` / `restricted` scope.

## Build Manifest

```
database/migrations/xxxx_create_dms_wiki_pages_table.php
database/migrations/xxxx_create_dms_wiki_page_versions_table.php
app/Models/DMS/{WikiPage,WikiPageVersion}.php
app/Data/DMS/{CreateWikiPageData,UpdateWikiPageData,WikiPageData}.php
app/Services/DMS/WikiService.php
app/Filament/DMS/Resources/WikiPageResource.php
app/Filament/DMS/Pages/WikiViewerPage.php
database/factories/DMS/WikiPageFactory.php
tests/Feature/DMS/{WikiPageTest,WikiAccessTest}.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Restricted page invisible in tree, search, AND direct viewer URL for a non-permitted user.
- [ ] Body purified (XSS fixture) before storage.
- [ ] Version snapshot per save; restore works; cap enforced.
- [ ] Page tree cycle rejected.
- [ ] Page-id links survive a page rename.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none)* | — | No cross-domain events; wiki is self-contained in v1 |
| Consumes | *(none)* | — | Reads no other domain's data |
| Reads (platform) | Tiptap editor + HTMLPurifier + `spatie/laravel-sluggable` + Meilisearch | platform packages | Rich text, slugs, and full-text index are platform reads, not domain edges |

**Data ownership:** `dms.wiki` writes only `dms_wiki_pages` and `dms_wiki_page_versions`. It never writes another domain's tables and shares no tables with the document library — a wiki page is a DB record, not a stored file ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../document-library/_module|Document Library]] (sibling DMS module — no dependency edge)
- [[../../../architecture/search]] · [[../../../architecture/packages]] (awcodes/filament-tiptap-editor)
