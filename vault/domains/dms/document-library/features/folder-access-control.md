---
domain: dms
module: document-library
feature: folder-access-control
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Folder Access Control

Restrict a folder (and its subtree) to specific roles or users; enforced uniformly across every access path.

## Behaviour

- A folder's `access_level` is `all` (every company user) or `restricted`.
- `restricted` → one or more `dms_folder_access` rows, each naming exactly one role OR one user.
- Restriction is **inherited down the subtree**: restricting a parent restricts all descendants; access is the intersection of ancestor rules *(assumed intersection semantics)*.
- `accessibleFoldersFor(User): Builder` resolves the full accessible set once; tree, grid, search, and direct viewer URL all compose on it.
- Restricted folders are **invisible** (not "locked") to non-permitted users — their existence is not disclosed.

## UI

- **Kind**: simple-resource (folder access config on `FolderResource`).
- **Page**: `FolderResource` form (`/dms/library/folders/{folder}/edit`).
- **Layout**: standard folder form + an "Access" section: level radio (all/restricted) → role/user multi-select repeater when restricted.
- **Key interactions**: toggle restricted → reveal role/user picker; save re-resolves the accessible set.
- **States**: empty (no restrictions → "Visible to everyone") · loading · error (validation: each row exactly one of role/user) · selected (n/a).
- **Gating**: `dms.library.manage-access`.

## Data

- Owns / writes: `dms_folders.access_level`, `dms_folder_access` (this module).
- Reads: `core.rbac` roles/users for the picker.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing.
- Shared entity: roles/users owned by `core.rbac` (read-only for the picker).

## Unknowns

- Intersection vs override semantics when a child adds its own rules on top of an inherited parent restriction *(assumed intersection)*.
- Whether owners always bypass restriction on their own folders — unspecified.

## Related

- [[../_module|Document Library]] · [[folder-browser]] · [[document-search]] · [[../security]]
