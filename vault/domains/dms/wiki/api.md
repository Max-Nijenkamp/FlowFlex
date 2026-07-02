---
domain: dms
module: wiki
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Wiki — API / DTOs

All input flows through `spatie/laravel-data` DTOs — never `$request->all()`.

## `CreateWikiPageData`

| Field | Type | Rules |
|---|---|---|
| `title` | string | required, max:255 |
| `body` | string | required, **purified** on set |
| `parent_page_id` | ulid | nullable, must be accessible, must **not** create a cycle |
| `access_level` | string | required, in `all,restricted`, default `all` |
| `access_list` | array | `required_if:access_level,restricted` — role/user ids |

## `UpdateWikiPageData`

Same fields as `CreateWikiPageData`, all optional except identity. On save through `WikiService::save`, the previous body is snapshotted to `dms_wiki_page_versions` before the update is written.

## `WikiPageData` (output)

Returned by `save` / `restoreVersion`: `id`, `title`, `slug`, `body` (rendered/purified), `parent_page_id`, `author_id`, `updated_by`, `access_level`, `updated_at`.

## Public / Portal Endpoints

None. Wiki is an internal `/dms` surface. There is no public or portal route in v1 — all reads go through `accessiblePagesFor` behind `canAccess()`.

> [!warning] UNVERIFIED
> The source `## DTOs` section only spells out `CreateWikiPageData`. `UpdateWikiPageData` (referenced by `WikiService::save`) and the `WikiPageData` output shape are inferred from the document-library convention *(assumed)*.
