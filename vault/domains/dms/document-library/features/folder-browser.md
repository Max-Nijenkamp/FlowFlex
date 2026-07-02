---
domain: dms
module: document-library
feature: folder-browser
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Folder Browser

The primary DMS surface: a nested folder tree beside a document grid, with drag-drop upload and access-aware navigation.

## Behaviour

- Left rail = folder tree (`parent_folder_id`), lazy-expanded; only folders in `accessibleFoldersFor(user)` render.
- Selecting a folder loads its documents into the grid (name, type icon, size, owner, modified).
- Drag-drop a file onto the grid → upload into the current folder ([[document-upload]]).
- Row actions: open (viewer), move/copy, favourite, delete (gated).
- Restricted folders and their subtree are simply absent for non-permitted users — no "locked" placeholder that reveals existence.

## UI

- **Kind**: custom-page (file browser / library — [[../../../../architecture/ui-strategy]] row #11 tree page).
- **Page**: "Document Library" (`/dms/library`).
- **Layout**: two-pane — folder tree sidebar (left), document grid (main); top toolbar with search box + upload button + breadcrumb.
- **Key interactions**: click folder → load grid; drag file → optimistic upload row + progress → replace with real row on complete; click document → open viewer; right-click / row menu → move/copy/favourite/delete.
- **States**: empty (folder has no documents → "Drop files here or upload" CTA) · loading (skeleton grid + tree spinner) · error (toast + retry on failed upload) · selected (highlighted folder + grid row).
- **Gating**: visible with `dms.library.view-any`; upload requires `dms.library.upload`; folder ops require `dms.library.manage-folders`.

## Data

- Owns / writes: `dms_folders`, `dms_documents`, `dms_favourites` (this module).
- Reads: `accessibleFoldersFor` (own scope); media via [[../../../core/file-storage/_module|core.files]].
- Cross-domain writes: none — bytes stored through the file-storage service ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing (no events v1).
- Shared entity: media/file record owned by `core.files`.

## Unknowns

- Multi-select bulk move/delete in v1? *(assumed out)*
- Grid vs list toggle, thumbnails for images — polish, unspecified.

## Related

- [[../_module|Document Library]] · [[document-viewer]] · [[document-upload]] · [[folder-access-control]]
