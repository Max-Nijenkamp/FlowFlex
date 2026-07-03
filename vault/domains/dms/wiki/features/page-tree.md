---
domain: dms
module: wiki
feature: page-tree
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Page Tree

The nested page hierarchy and navigation sidebar — a cycle-checked `parent_page_id` tree, access-filtered per user.

## Behaviour

- Pages nest via `parent_page_id` (self-referential); the tree is built by `WikiService::tree()`.
- **Cycle-checked** — a page cannot become its own ancestor; the editor rejects a parent selection that would form a loop.
- The tree is **access-filtered** through `accessiblePagesFor(user)` — restricted pages the user can't see never appear as nodes.
- Drives the nav sidebar in the viewer and the parent picker in the editor.

## UI

- **Kind**: custom-page (the nav sidebar is **part of the [[wiki-viewer|Wiki Viewer]] custom page**, not its own route).
- **Page**: nested nav rail within "Wiki" (`/dms/wiki`).
- **Layout**: collapsible tree of page titles; current page highlighted; expand/collapse per branch.
- **Key interactions**: click node → open that page in the viewer; expand/collapse branch; (editor reuses the same tree as a parent select).
- **States**: empty (no pages → "Create the first wiki page" CTA) · loading (tree skeleton) · error (toast + retry) · selected (active page node highlighted).
- **Gating**: `dms.wiki.view-any` + per-page access post-filter.

## Data

- Owns / writes: none (read-only view over `dms_wiki_pages`).
- Reads: `dms_wiki_pages` via `WikiService::tree` / `accessiblePagesFor` (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: navigation into [[wiki-viewer|Wiki Viewer]]; parent selection into [[page-editor|Page Editor]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] `WikiService::tree()` nests by `parent_page_id`; branch ordering alphabetical *(assumed)*

### Feature (Pest)
- [ ] Tree is access-filtered: restricted pages absent from a non-permitted user's tree
- [ ] Tenant isolation: tree contains only own-company pages; `dms.wiki.view-any` required

### Livewire
- [ ] Viewer nav tree renders nodes, highlights the active page, expand/collapse works; empty state shows "Create the first wiki page" CTA

## Unknowns

- Whether restricting a parent hides its descendants in the tree, or gating is per-page — [[../unknowns]].
- Ordering within a branch (manual sort vs title) is unspecified *(assumed alphabetical)*.

## Related

- [[../_module|Wiki]] · [[wiki-viewer]] · [[page-editor]] · [[page-access-control]]
