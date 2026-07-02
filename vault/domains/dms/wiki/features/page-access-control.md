---
domain: dms
module: wiki
feature: page-access-control
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Page Access Control

Per-page visibility — a page is open to `all` company users or `restricted` to a role/user list, enforced everywhere by one scope.

## Behaviour

- `access_level` is `all` (default) or `restricted`; when `restricted`, `access_list` (jsonb) holds the permitted role/user ids.
- `WikiService::accessiblePagesFor(User)` is the single source of truth — a restricted page is invisible in the **tree**, in **search results**, AND on the **direct viewer URL** for a non-permitted user.
- The access list is a **second gate** on top of the `dms.wiki.view-any` permission.

## UI

- **Kind**: simple-resource (an access **form section** on `WikiPageResource`, not its own page).
- **Page**: "Access" section within the [[page-editor|Page Editor]] form (`/dms/wiki-pages/{record}/edit`).
- **Layout**: `access_level` toggle (`all` / `restricted`); when `restricted`, a role/user multi-select (`access_list`) appears.
- **Key interactions**: switch to `restricted` → reveal + require the list; save → scope applied immediately across tree/search/viewer.
- **States**: empty (`all` — no list shown) · loading (save spinner) · error (`restricted` with empty list → validation) · selected (chosen roles/users chipped).
- **Gating**: `dms.wiki.manage-access` to edit the section; the resulting scope gates every read via `dms.wiki.view-any`.

## Data

- Owns / writes: `access_level`, `access_list` on `dms_wiki_pages` (via `WikiService::save`).
- Reads: roles/users for the picker via `core.rbac` read API.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: role/user reference data from [[../../../core/rbac/_module|core.rbac]].
- Feeds: the `accessiblePagesFor` scope used by [[page-tree|Page Tree]], [[wiki-viewer|Wiki Viewer]], and [[wiki-search|Wiki Search]].
- Shared entity: roles/users (owned by `core.rbac`).

## Unknowns

- Whether restriction **inherits** down the `parent_page_id` tree, or is strictly per-page (*(assumed)* per-page, non-inherited) — [[../unknowns]].
- Interaction of `access_list` role ids vs user ids resolution order — unspecified.

## Related

- [[../_module|Wiki]] · [[page-tree]] · [[wiki-viewer]] · [[wiki-search]] · [[../../../../security/data-ownership]]
