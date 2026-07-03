---
domain: dms
module: wiki
feature: page-history
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Page History

Per-page version snapshots with restore — an append-only body history captured on every save.

## Behaviour

- Every update through `WikiService::save` appends the **previous body** to `dms_wiki_page_versions` (with `edited_by`, `edited_at`).
- Append-only, capped 50 rows per page *(assumed)* — oldest pruned.
- `WikiService::restoreVersion(versionId)` sets a snapshot's body back onto the page; the restore itself creates a new snapshot of the pre-restore state *(assumed)*.

## UI

- **Kind**: simple-resource (version-history **relation manager** on `WikiPageResource`).
- **Page**: "Versions" relation tab within a wiki page (`/dms/wiki-pages/{record}/edit` → Versions).
- **Layout**: table of snapshots — `edited_by`, `edited_at`, (diff/preview action); newest first.
- **Key interactions**: click a version → preview its body; **Restore** row action → confirm → body reverted + new snapshot; no create/edit (append-only).
- **States**: empty (no prior versions → "No history yet") · loading (table skeleton) · error (restore fails → toast) · selected (previewed version highlighted).
- **Gating**: view with `dms.wiki.view-any`; restore requires `dms.wiki.update`.

## Data

- Owns / writes: `dms_wiki_page_versions` (append) + `dms_wiki_pages` (on restore), via `WikiService`.
- Reads: `dms_wiki_page_versions` scoped to the page (own module).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: snapshots produced by [[page-editor|Page Editor]] on each save.
- Feeds: a restore updates the body shown by [[wiki-viewer|Wiki Viewer]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Cap enforcement: 51st snapshot prunes the oldest (cap 50 *(assumed)*)

### Feature (Pest)
- [ ] Restore sets the snapshot body onto the page AND creates a new snapshot of the pre-restore state *(assumed)*
- [ ] Snapshots are append-only — no update/delete path exposed
- [ ] Tenant isolation: versions of company A's page invisible to company B; restore requires `dms.wiki.update`

### Livewire
- [ ] Versions relation manager: newest first, preview action works, Restore confirms then reverts; no create/edit actions
- [ ] Restore action hidden without `dms.wiki.update`

## Unknowns

- Cap value (50 *(assumed)*) and prune-vs-archive — [[../unknowns]].
- Whether a diff view (vs plain preview) is in v1 scope *(assumed preview only)*.
- Restore-creates-snapshot semantics *(assumed yes)* — [[../decisions]].

## Related

- [[../_module|Wiki]] · [[page-editor]] · [[wiki-viewer]]
