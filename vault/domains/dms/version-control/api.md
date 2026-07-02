---
domain: dms
module: version-control
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Version Control — API / DTOs

All input flows through `spatie/laravel-data` DTOs — never `$request->all()`.

## `UploadVersionData`

| Field | Type | Rules |
|---|---|---|
| `document_id` | ulid | required; must be accessible (via `dms.library`'s `accessibleFoldersFor`) and either **unlocked or locked by the current user** |
| `file` | uploaded file | required; reuses the `dms.library` allowed MIME/extension whitelist + max size per [[../../../architecture/security]] baseline (stated explicitly — see [[security]]) |
| `change_note` | string | nullable |

## `RestoreVersionData`

| Field | Type | Rules |
|---|---|---|
| `version_id` | ulid | required; must be a version of an accessible document |

## `VersionData` (output)

Returned by `uploadVersion` / `restore`: `id`, `document_id`, `version_number`, `uploaded_by`, `change_note`, `is_current`, `created_at`, download URL (temp signed).

## Public / Portal Endpoints

None. Version control is an internal `/dms` surface hosted on the Document Viewer. Historical-version download uses **temporary signed URLs** minted server-side, gated by folder access — no public route.
