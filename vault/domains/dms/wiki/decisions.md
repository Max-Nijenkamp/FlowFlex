---
domain: dms
module: wiki
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Wiki — Decisions

## ADR: Wiki is standalone within DMS

- **Context:** The wiki lives in the `/dms` panel alongside the document library, so a shared-storage or shared-access edge would be tempting.
- **Decision:** Wiki depends only on `core.billing` + `core.rbac`. A wiki page is a database record (rich text in `dms_wiki_pages`), **not** a stored file — it shares no tables, no folders, and no access scope with the document library.
- **Consequences:** No `dms.library` dependency edge; the two modules can ship and evolve independently. Cross-module federated search (wiki + documents in one index) is deferred, not assumed ([[unknowns]]).

## ADR: Single `accessiblePagesFor()` scope for all access paths

- **Context:** Page restrictions must hold across the tree, search results, and the direct viewer URL. Independent checks would drift.
- **Decision:** One `WikiService::accessiblePagesFor(User): Builder` resolves access once; every list / tree / search / viewer path composes on it.
- **Consequences:** No path can leak a restricted page without breaking the others; single point to test. Search must post-filter on the accessible set rather than trusting Meilisearch alone.

## ADR: Body purified before storage

- **Decision:** Tiptap HTML is run through `ezyang/htmlpurifier` at write time; the stored `body` is already safe.
- **Consequences:** Viewers render trusted HTML without re-sanitising; XSS is contained at the single write boundary.

## ADR: Version snapshot per save, capped 50 *(assumed)*

- **Context:** Page history needs the prior state, but unbounded snapshots grow without limit.
- **Decision:** `WikiService::save` appends the previous body to `dms_wiki_page_versions` on every update; the table is capped at 50 rows per page *(assumed)* — oldest pruned.
- **Consequences:** History is bounded and cheap; deep-history recovery beyond 50 edits is out of scope. Cap value unverified ([[unknowns]]).

## ADR: Internal links stored as page-id, not slug *(assumed)*

- **Decision:** Internal page links picked in the editor are stored as page-id references so they survive a target page's rename/re-slug *(assumed)*.
- **Consequences:** Renaming a page never breaks inbound links; the viewer resolves page-id → current slug/title at render time.
