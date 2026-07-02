---
domain: dms
module: document-library
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Library — Unknowns

## Assumed Items

- `dms_favourites` table shape (`user_id` + `document_id` unique) *(assumed)* — favourites mechanism not in source data model.
- Folder name uniqueness `(parent_folder_id, name)` *(assumed)*.
- PDF-only text extraction via `pdftotext`; other types name-only *(assumed)*.
- Office documents preview by download, not inline *(assumed)*.
- No column encryption on document bytes/metadata *(assumed)*.

## Open Questions

- Should a `DocumentUploaded` / `DocumentDeleted` cross-domain event be fired so other domains (e.g. audit, search analytics) can react? Currently none.
- Full-text extraction for Office/CSV formats — which engine, on which queue?
- Copy semantics: does a copied document get a fresh media object or a reference? (Assumed fresh copy.)
- Bulk operations (multi-select move/delete) — in scope for v1 library page?
- Trash/restore UX for soft-deleted documents vs retention soft-delete — do they share a bin?
