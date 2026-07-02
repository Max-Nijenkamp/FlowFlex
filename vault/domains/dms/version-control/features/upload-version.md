---
domain: dms
module: version-control
feature: upload-version
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Upload Version

Upload a new version of an existing document — replaces the current file, keeps the old one as history, and refreshes the document's searchable metadata.

## Behaviour

1. Validate `UploadVersionData`: `document_id` accessible (via `dms.library`'s `accessibleFoldersFor`) and **unlocked or locked by the current user**; file passes the reused `dms.library` MIME/extension whitelist + max size ([[../security]]).
2. In a transaction: compute the next `version_number` (sequential, unique per document), store bytes via `core.files` `CompanyPathGenerator` under `companies/{id}/dms/`.
3. Flip `is_current` — the new version becomes current, the previous one drops to history (partial-unique on `(document_id)` where `is_current`).
4. Update document metadata (size / mime / extracted text) through `dms.library`'s `DocumentService`, which re-dispatches `ExtractDocumentTextJob` — never write `dms_documents` directly.
5. If the document is locked by **another** user → throw `DocumentLockedException`, no version written.

## UI

- **Kind**: action  <!-- header action on the DocumentViewerPage custom page -->
- **Page**: "Document Viewer" (`/dms/library` viewer) — "Upload new version" header action.
- **Layout**: modal with a file drop-zone + `change_note` textarea; the version-history list refreshes on success.
- **Key interactions**: click action → modal → drop file → progress → success toast + history row appended; disallowed type/oversize → inline rejection before upload; document locked by another → blocked toast surfacing `DocumentLockedException`.
- **States**: empty (n/a) · loading (upload progress bar) · error (rejected type / oversize / locked / storage failure → toast + retry) · selected (n/a).
- **Gating**: `dms.versions.upload` + folder access; lock check enforced server-side.

## Data

- Owns / writes: `dms_document_versions` (this module).
- Reads/Commands: `dms.library` `DocumentService` (metadata update + text-extraction re-trigger); `core.files` storage service (owns the media record + bytes).
- Cross-domain writes: none — document metadata updated via `DocumentService`, bytes via the file-storage service, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing v1 (a `DocumentVersionUploaded` event is an open question — [[../unknowns]]).
- Shared entity: `dms_documents` (`dms.library`), media record (`core.files`).

## Unknowns

- Whether to fire `DocumentVersionUploaded` for audit/approvals — open ([[../unknowns]]).
- That metadata flows via `DocumentService` is *(assumed)* — source names the effect, not the API.

## Related

- [[../_module|Version Control]] · [[version-history]] · [[restore-version]] · [[document-locking]]
- [[../../document-library/_module|Document Library]]
