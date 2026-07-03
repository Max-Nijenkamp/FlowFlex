---
domain: dms
module: version-control
feature: restore-version
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Restore Version

Make an older version current again — by creating a **new** version, never deleting or overwriting history.

## Behaviour

1. Validate `RestoreVersionData`: `version_id` belongs to an accessible document.
2. `VersionService::restore` copies the target historical version's `media_id` into a fresh version row (next `version_number`, `is_current = true`).
3. The previous current version drops to history; the restored content is now live. Nothing is deleted — the full trail is preserved.
4. Document metadata (size / mime / extracted text) is refreshed through `dms.library`'s `DocumentService`, exactly as on upload.

## UI

- **Kind**: action  <!-- row/header action on the DocumentViewerPage version-history list -->
- **Page**: "Document Viewer" (`/dms/library` viewer) — "Restore" row action in the version-history list.
- **Layout**: confirmation dialog naming the version number + upload date being restored; history list refreshes on success.
- **Key interactions**: click restore → confirm modal → new current version created → success toast + history row appended (never removed).
- **States**: empty (n/a) · loading (spinner during copy) · error (toast + retry) · selected (target version row highlighted in the confirm dialog).
- **Gating**: `dms.versions.restore` + folder access.

## Data

- Owns / writes: `dms_document_versions` (this module).
- Reads/Commands: `dms.library` `DocumentService` (metadata refresh); `core.files` (media copy/reference).
- Cross-domain writes: none — metadata via `DocumentService`, media via the file-storage service, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing v1 (a `DocumentRestored` event is an open question — [[../unknowns]]).
- Shared entity: `dms_documents` (`dms.library`), media record (`core.files`).

## Test Checklist

### Unit
- [ ] `RestoreVersionData` validation: `version_id` resolves to an accessible document.

### Feature (Pest)
- [ ] Restore creates a new current version referencing the target's media, flips `is_current`, and deletes nothing (history intact).
- [ ] Document metadata is refreshed via `DocumentService` on restore; a company A user cannot restore a company B version (tenant isolation).

### Livewire
- [ ] Restore row action shows a confirm dialog naming the version; on confirm the history list appends the new current version.
- [ ] Action denied without `dms.versions.restore` + folder access.

## Unknowns

- Whether restore duplicates the media object or references it — assumed a new version pointing at the same/copied `media_id` ([[../unknowns]]).
- Whether `dms.versions.restore` implies `dms.versions.upload` or is independent — open ([[../unknowns]]).

## Related

- [[../_module|Version Control]] · [[upload-version]] · [[version-history]] · [[document-locking]]
- [[../../document-library/_module|Document Library]]
