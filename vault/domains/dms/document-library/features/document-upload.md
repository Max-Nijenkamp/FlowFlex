---
domain: dms
module: document-library
feature: document-upload
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Document Upload

Upload a file into a folder, store it under the tenant path, and kick off text extraction for search.

## Behaviour

1. Validate `UploadDocumentData`: target folder accessible; file passes MIME/extension whitelist + max-size ([[../security]]).
2. Store bytes via `core.files` `CompanyPathGenerator` under `companies/{id}/dms/`.
3. Write the `dms_documents` row (name, slug, size, mime, owner, tags).
4. Dispatch `ExtractDocumentTextJob` — PDF text (`pdftotext`) → `extracted_text` → reindex in Meilisearch. Non-PDF: name/metadata only v1 *(assumed)*.
5. Rate-limited per company/user to protect storage + Meilisearch.

## UI

- **Kind**: custom-page (part of the [[folder-browser|Folder Browser]] page — drag-drop zone + upload button).
- **Page**: within "Document Library" (`/dms/library`).
- **Layout**: drop-zone overlay on the grid + a modal for name/description/tags on upload.
- **Key interactions**: drag file → optimistic progress row → real row on complete; disallowed type → inline rejection toast before upload.
- **States**: empty (n/a) · loading (per-file progress bars) · error (rejected type / oversize / storage failure → toast + retry) · selected (n/a).
- **Gating**: `dms.library.upload` + target folder access.

## Data

- Owns / writes: `dms_documents` (this module).
- Reads/Commands: `core.files` storage service (owns the media record + bytes).
- Cross-domain writes: none — bytes written through the file-storage service, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing v1 (a `DocumentUploaded` event is an open question — [[../unknowns]]).
- Shared entity: media record (`core.files`).

## Unknowns

- Whether to fire `DocumentUploaded` for audit/analytics — open ([[../unknowns]]).
- Extraction engine for non-PDF types — deferred.

## Related

- [[../_module|Document Library]] · [[folder-browser]] · [[document-search]]
