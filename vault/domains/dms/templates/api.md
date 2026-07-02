---
domain: dms
module: templates
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Templates — API / DTOs

All input flows through `spatie/laravel-data` DTOs — never `$request->all()`.

## `GenerateDocumentData`

| Field | Type | Rules |
|---|---|---|
| `template_id` | ulid | required; template in this company |
| `target_folder_id` | ulid | required; must be **accessible** via `dms.library` `accessibleFoldersFor` |
| `merge_source` | object | `{ type: employee\|contact\|manual, id? }` — `id` required unless `manual` |
| `manual_values` | object | Covers **all** declared fields after source resolution — "All merge fields must have a value." |
| `output` | string | `in:document,pdf` |

Resolution order: the chosen `merge_source` provider fills whatever declared fields it whitelists; `manual_values` fills the rest. Any declared field still empty after both → generation blocked with the "all merge fields must have a value" error.

## `DocumentData` (output)

Generation returns the library's own `DocumentData` (see [[../document-library/api]]) — `id`, `name`, `slug`, `folder_id`, `owner_id`, `file_size`, `mime_type`, download URL (temp signed) — since the document is created **through** `DocumentService::upload`.

## Public / Portal Endpoints

None. Templates is an internal `/dms` surface. The generate action is a gated Filament page action, rate-limited per company/user ([[security]]).
