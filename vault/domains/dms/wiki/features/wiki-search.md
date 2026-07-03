---
domain: dms
module: wiki
feature: wiki-search
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Wiki Search

Full-text search across page titles and stripped bodies — always post-filtered by page access.

## Behaviour

- Meilisearch index over `title` + stripped `body`.
- Every query is post-filtered by `accessiblePagesFor(user)` — a restricted page never surfaces, even on a direct term hit.
- Rate-limited per company/user to protect the Meilisearch instance.
- Wiki and library documents are assumed to use **separate** indexes (no federated search v1) *(assumed)*.

## UI

- **Kind**: custom-page (search box in the [[wiki-viewer|Wiki Viewer]] toolbar + results overlay).
- **Page**: within "Wiki" (`/dms/wiki?q=`).
- **Layout**: top search field → results list (title, breadcrumb of parent pages, snippet) replacing the rendered page while a query is active.
- **Key interactions**: type → debounced query → results; click result → open that page in the viewer; clear → back to the current page.
- **States**: empty (no matches → "No pages match") · loading (result skeletons) · error (search backend down → toast + retry) · selected (result highlighted).
- **Gating**: `dms.wiki.view-any` + per-page access post-filter.

## Data

- Owns / writes: reads `dms_wiki_pages`; no writes.
- Reads: Meilisearch index + `accessiblePagesFor` scope (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: navigation into [[wiki-viewer|Wiki Viewer]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Index payload: title + stripped body (no HTML tags) sent to Meilisearch on save

### Feature (Pest)
- [ ] Restricted page never surfaces in results for a non-permitted user, even on a direct term hit (post-filter)
- [ ] Search endpoint rate-limited per company/user; over-limit returns 429
- [ ] Tenant isolation: results never include another company's pages

### Livewire
- [ ] Debounced query renders results overlay; clear returns to the current page; backend-down shows toast + retry

## Unknowns

- Whether wiki + documents share one federated index or stay separate (*(assumed)* separate) — [[../unknowns]].
- Sync vs queued reindex on save (*(assumed)* sync via Scout) — [[../architecture]].

## Related

- [[../_module|Wiki]] · [[wiki-viewer]] · [[page-access-control]] · [[../../../../architecture/search]]
