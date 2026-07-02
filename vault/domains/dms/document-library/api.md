---
domain: dms
module: document-library
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Library — API / DTOs

All input flows through `spatie/laravel-data` DTOs — never `$request->all()`.

## `UploadDocumentData`

| Field | Type | Rules |
|---|---|---|
| `folder_id` | ulid | required, must be accessible via `accessibleFoldersFor` |
| `file` | uploaded file | required; allowed MIME/extension whitelist + max size per [[../../../architecture/security]] baseline (stated explicitly, not link-only — see [[security]]) |
| `name` | string | nullable, default filename |
| `description` | string | nullable |
| `tags` | array | nullable, string tags |

## `MoveDocumentData`

| Field | Type | Rules |
|---|---|---|
| `document_id` | ulid | required |
| `target_folder_id` | ulid | required, accessible, ≠ current folder |

## `DocumentData` (output)

Returned by `upload` / `move` / `copy`: `id`, `name`, `slug`, `folder_id`, `owner_id`, `file_size`, `mime_type`, `is_archived`, download URL (temp signed).

## Public / Portal Endpoints

None. Document Library is an internal `/dms` surface. Preview/download uses **temporary signed URLs** minted server-side, gated by folder access — no public route.
