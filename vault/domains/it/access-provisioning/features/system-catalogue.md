---
domain: it
module: access-provisioning
feature: system-catalogue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# System Catalogue

The catalogue of tools/systems the company uses (Google Workspace, Slack, GitHub, AWS, …). Every access
grant and template references a system from this list.

- Simple Filament resource over `it_systems` ([[../../../../architecture/patterns/feature-ui-spec]]).
- Each system: name, description, owner.

## UI

- **Kind**: simple-resource — CRUD of `it_systems`.
- **Page**: `SystemResource` at `/it/systems`.
- **Layout**: table columns name · description · owner · # active grants; form fields name (required), description, owner (user select).
- **Key interactions**: create / edit / delete a system; deleting a system in use prompts to reassign or block *(assumed)*.
- **States**: empty (no systems → "add your first tool" CTA) · loading (table skeleton) · error (toast) · selected (edit form).
- **Gating**: view `it.access.view-any`; create/edit/delete `it.access.manage-systems`.

## Data

- Owns / writes: `it_systems` only.
- Reads: users (for owner select).
- Cross-domain writes: none — cross-domain effects flow through events only, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `it_systems` rows referenced by [[access-grants]] and [[access-templates]].
- Shared entity: none owned elsewhere.

## Unknowns

- Delete behaviour when a system has active grants (block vs. reassign) — `*(assumed)*`.

## Related

- [[../_module|Access Provisioning]] · [[access-grants]] · [[access-templates]] · [[../data-model|data-model]]
