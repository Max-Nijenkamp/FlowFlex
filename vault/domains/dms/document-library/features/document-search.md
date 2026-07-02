---
domain: dms
module: document-library
feature: document-search
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Document Search

Full-text search across document names, descriptions, and extracted text — always post-filtered by folder access.

## Behaviour

- Meilisearch index over `name`, `description`, `extracted_text`; `folder_id` is a filterable attribute.
- Every query is post-filtered by `accessibleFoldersFor(user)` — a restricted folder's documents never surface, even on a direct hit.
- Extracted text comes from `ExtractDocumentTextJob` (PDF v1); non-PDF matches on name/metadata only.
- Rate-limited per company/user to protect the Meilisearch instance.

## UI

- **Kind**: custom-page (search box in the [[folder-browser|Folder Browser]] toolbar + results overlay).
- **Page**: within "Document Library" (`/dms/library?q=`).
- **Layout**: top search field → results list (name, folder breadcrumb, snippet, type icon) replacing the grid while a query is active.
- **Key interactions**: type → debounced query → results; click result → open viewer; clear → back to folder grid.
- **States**: empty (no matches → "No documents match") · loading (result skeletons) · error (search backend down → toast + retry) · selected (result highlighted).
- **Gating**: `dms.library.view-any` + folder-access post-filter.

## Data

- Owns / writes: reads `dms_documents`; no writes.
- Reads: Meilisearch index + `accessibleFoldersFor` scope (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing.
- Shared entity: none.

## Unknowns

- Non-PDF full-text extraction engine — deferred ([[../unknowns]]).
- Whether wiki pages and documents share one federated search index or stay separate — see [[../../wiki/_module|Wiki]] (*(assumed)* separate).

## Related

- [[../_module|Document Library]] · [[document-upload]] · [[folder-access-control]] · [[../../../architecture/search]]
