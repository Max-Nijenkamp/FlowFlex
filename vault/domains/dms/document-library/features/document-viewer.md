---
domain: dms
module: document-library
feature: document-viewer
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Document Viewer

In-browser preview of a single document with its metadata and (when active) version history.

## Behaviour

- Preview PDFs + images inline via a **temporary signed URL** minted server-side; Office docs offer download only (v1 *(assumed)*).
- Right/side panel shows metadata: name, folder, owner, size, mime, tags, created/modified, favourite toggle.
- If [[../../version-control/_module|dms.versions]] is active, a version-history relation renders here (download / restore).
- Direct viewer URL re-checks folder access — a non-permitted user gets 403/not-found, never the file.

## UI

- **Kind**: custom-page ([[../../../../architecture/ui-strategy]] row #2-style viewer).
- **Page**: "Document Viewer" (`/dms/library/{document}`).
- **Layout**: main preview pane (PDF/image canvas) + right metadata rail; version-history accordion when versions module active.
- **Key interactions**: scroll/zoom preview; download button (signed URL); favourite toggle; move/copy action; open version history.
- **States**: empty (unsupported type → "Preview not available, download instead") · loading (preview skeleton) · error (expired signed URL → re-mint + retry) · selected (n/a, single doc).
- **Gating**: `dms.library.view-any` + folder access; download inherits the same gate.

## Data

- Owns / writes: `dms_documents` (favourite via `dms_favourites`).
- Reads: temp signed URL from [[../../../core/file-storage/_module|core.files]]; version relation from `dms.versions` (soft).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing.
- Shared entity: media record (`core.files`); `dms_document_versions` (owned by `dms.versions`).

## Test Checklist

### Unit
- [ ] Preview eligibility resolves to inline (PDF/image) vs download-only (Office) by mime.

### Feature (Pest)
- [ ] Preview mints a short-lived signed URL; the direct viewer URL 403s for a user without folder access (never serves the file).
- [ ] A user in company A cannot open a company B document by id (tenant isolation).

### Livewire
- [ ] An expired signed URL re-mints and retries; favourite toggle persists.
- [ ] Version-history relation renders only when `dms.versions` is active; page gated on `dms.library.view-any` + folder access.

## Unknowns

- Inline Office preview (needs a converter/viewer) — deferred *(assumed)*.
- Signed-URL TTL value — pull from security baseline, unspecified here.

## Related

- [[../_module|Document Library]] · [[folder-browser]] · [[../../version-control/_module|Version Control]]
