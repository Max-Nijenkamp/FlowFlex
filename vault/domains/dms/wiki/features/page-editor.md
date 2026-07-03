---
domain: dms
module: wiki
feature: page-editor
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Page Editor

Create and edit a wiki page with a rich-text Tiptap editor; every save purifies the body and snapshots the previous version.

## Behaviour

- Title, body (rich text), parent page, and access config edited in one form.
- Body edited via `awcodes/filament-tiptap-editor`; **purified with `ezyang/htmlpurifier` before storage** (stored HTML is already safe).
- Slug generated from title via `spatie/laravel-sluggable`, unique per company.
- `parent_page_id` picked from the tree; a selection that would create a cycle is rejected.
- Internal links inserted via a page picker, stored as **page-id** links so they survive target renames *(assumed)*.
- On **update**, `WikiService::save` appends the previous body to `dms_wiki_page_versions` before writing (capped 50 *(assumed)*).

## UI

- **Kind**: simple-resource (create/edit form on `WikiPageResource`).
- **Page**: "Wiki" — `WikiPageResource` create/edit (`/dms/wiki-pages/create`, `/{record}/edit`).
- **Layout**: title field → Tiptap body editor (full width) → parent-page select → access section (`all` / `restricted` + role/user list, shown only when restricted).
- **Key interactions**: type body → purify on submit; pick parent → cycle-checked; save → version snapshot + reindex → redirect to viewer.
- **States**: empty (new page, blank editor) · loading (save spinner) · error (validation: cycle, missing access list when restricted) · selected (editing existing record).
- **Gating**: `dms.wiki.create` to create, `dms.wiki.update` to edit; access section requires `dms.wiki.manage-access`.

## Data

- Owns / writes: `dms_wiki_pages`, `dms_wiki_page_versions` (via `WikiService::save`).
- Reads: the page tree via `WikiService::accessiblePagesFor` (own module) for the parent picker.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: version rows consumed by [[page-history|Page History]]; rendered output shown by [[wiki-viewer|Wiki Viewer]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Cycle check: parent selection that would make a page its own ancestor is rejected
- [ ] Slug generation unique per company; body purified (script tags stripped) before storage

### Feature (Pest)
- [ ] Update via `WikiService::save` snapshots the previous body to `dms_wiki_page_versions` before writing
- [ ] Concurrent edit: stale version-checked save conflicts instead of silently overwriting (optimistic locking)
- [ ] Tenant isolation: page create/edit scoped by company; `dms.wiki.create`/`dms.wiki.update` enforced

### Livewire
- [ ] Editor form validates: cycle-forming parent rejected, restricted access with empty list rejected
- [ ] canAccess(): create page hidden without `dms.wiki.create`; access section hidden without `dms.wiki.manage-access`

## Unknowns

- Version cap value (50 *(assumed)*) and prune-vs-archive — [[../unknowns]].
- Whether internal links are stored as page-id (assumed) or slug — [[../decisions]].
- Concurrent-edit handling (lock / last-writer-wins) — [[../unknowns]].

## Related

- [[../_module|Wiki]] · [[wiki-viewer]] · [[page-history]] · [[page-tree]] · [[../../../../architecture/packages]] (tiptap)
