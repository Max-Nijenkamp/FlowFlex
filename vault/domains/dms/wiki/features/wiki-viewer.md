---
domain: dms
module: wiki
feature: wiki-viewer
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Wiki Viewer

The read surface — a rendered wiki page with an auto table of contents and the nested nav sidebar.

## Behaviour

- Renders the purified `body` HTML of one page.
- **Auto table of contents** built from the body's headings (client-side *(assumed)*), anchor-linked for in-page jumps.
- Internal page-id links resolve to the current slug/title at render time — they survive target renames *(assumed)*.
- The nested nav sidebar ([[page-tree|Page Tree]]) is embedded here.
- A direct URL to a **restricted** page the user can't access returns not-found / forbidden — the viewer composes on `accessiblePagesFor`.

## UI

- **Kind**: custom-page (`WikiViewerPage`, #2-style — [[../../../../architecture/ui-strategy]]).
- **Page**: "Wiki" — `WikiViewerPage` (`/dms/wiki/{slug}`).
- **Layout**: three-pane — left nav tree · centre rendered page · right auto-TOC rail (sticky, tracks scroll).
- **Key interactions**: click TOC heading → smooth-scroll to section; click internal link → navigate to target page; click nav node → switch page; edit/favourite actions in the header.
- **States**: empty (page has no headings → TOC hidden) · loading (content skeleton) · error (restricted / missing → 404) · selected (active TOC heading highlighted on scroll).
- **Gating**: `dms.wiki.view-any` + per-page access post-filter; explicit `canAccess()` on the custom page.

## Data

- Owns / writes: none (read-only render).
- Reads: `dms_wiki_pages` via `accessiblePagesFor` (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: rendered body + versions produced by [[page-editor|Page Editor]]; nav from [[page-tree|Page Tree]].
- Feeds: nothing.
- Shared entity: none.

## Unknowns

- Auto-TOC client-side vs server-side heading parse *(assumed client-side)* — [[../unknowns]].
- Whether internal links resolve by page-id (assumed) — [[../decisions]].

## Related

- [[../_module|Wiki]] · [[page-tree]] · [[page-editor]] · [[page-history]] · [[wiki-search]]
