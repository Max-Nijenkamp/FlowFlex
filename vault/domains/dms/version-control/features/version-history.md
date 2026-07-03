---
domain: dms
module: version-control
feature: version-history
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Version History

View the full version history of a document — every version with its metadata — and download any historical version (folder-access gated).

## Behaviour

1. List all `dms_document_versions` for the document, newest first, showing `version_number`, uploader, upload date, `change_note`, and size/date compared to the current version (metadata-only diff; full content diff out of scope v1 *(assumed)*).
2. The current version is flagged (`is_current`).
3. Download any historical version via a temporary signed URL — **second-gated by folder access** (via `dms.library`'s `accessibleFoldersFor`), not just the module permission.
4. Per-row restore action links to [[restore-version|Restore Version]].

## UI

- **Kind**: custom-page  <!-- version-history relation manager on the DocumentViewerPage custom page -->
- **Page**: "Document Viewer" (`/dms/library` viewer) — version-history relation manager / panel.
- **Layout**: table of versions (number · uploader · date · change note · size vs current) with row actions (download, restore); current version badge-highlighted.
- **Key interactions**: click download → signed URL → file; click restore → confirm → new current version (see [[restore-version]]).
- **States**: empty (single version → "no previous versions yet") · loading (skeleton rows) · error (toast + retry) · selected (current-version row highlighted).
- **Gating**: `dms.versions.view-any` *(see [[../unknowns]] — UNVERIFIED)* + folder access; download re-checks folder access server-side.

## Data

- Owns / writes: nothing (read-only view over `dms_document_versions`, this module).
- Reads/Commands: `dms.library` `DocumentService` for folder-access scope; `core.files` for signed download URLs.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing.
- Shared entity: `dms_documents` (`dms.library`), media records (`core.files`).

## Test Checklist

### Unit
- [ ] History orders versions newest-first and flags the single `is_current` row; size/date diff computed against current.

### Feature (Pest)
- [ ] A user without folder access on the document cannot list history or download a historical version (second gate), even with the module permission.
- [ ] Company A cannot read company B's version history (tenant isolation); download uses a short-lived signed URL.

### Livewire
- [ ] History relation renders versions with download + restore row actions; current version highlighted.
- [ ] Rendered only when gated by the module permission + folder access.

## Unknowns

- `dms.versions.view-any` is referenced by the access contract but absent from the source Permissions list — UNVERIFIED ([[../unknowns]]).
- Metadata-only comparison (size/date) for v1; full content diff deferred *(assumed)*.

## Related

- [[../_module|Version Control]] · [[upload-version]] · [[restore-version]] · [[document-locking]]
- [[../../document-library/_module|Document Library]]
