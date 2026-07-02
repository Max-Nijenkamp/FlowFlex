---
domain: dms
module: wiki
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Wiki — Unknowns

## Assumed Items

- Auto-TOC is built **client-side** from the rendered body's headings *(assumed)* — source doesn't name a server-side heading parser.
- Internal page links stored as **page-id** so they survive renames *(assumed)*.
- Version snapshots **capped at 50** per page, oldest pruned *(assumed)*.
- Page access is **per-page, non-inherited** down the `parent_page_id` tree *(assumed)* — unlike folder access in the library.
- `UpdateWikiPageData` and the `WikiPageData` output shape inferred from library convention *(assumed)*.
- `WikiService` needs no interface (single implementation) *(assumed)*.
- Meilisearch reindex is synchronous via Scout, no dedicated queue job *(assumed)*.
- No column encryption on wiki bodies *(assumed)*.

## Open Questions

- **Favourite pages storage** — a stated core feature with no table in the source data model. Own `dms_wiki_favourites` table, a shared `dms_favourites` table, or a per-user pivot?
- **Access inheritance** — does restricting a parent page hide its descendants in the tree, or is each page gated independently?
- **Version cap** — is 50 the real limit, and are snapshots pruned or archived?
- **Federated search** — do wiki pages and library documents share one Meilisearch index, or stay separate? (Assumed separate — see [[../document-library/features/document-search|Document Search]].)
- **Restore semantics** — does `restoreVersion` create a fresh snapshot of the pre-restore state (assumed yes)?
- **Concurrent edits** — is there any lock/last-writer-wins handling for two users editing one page?
