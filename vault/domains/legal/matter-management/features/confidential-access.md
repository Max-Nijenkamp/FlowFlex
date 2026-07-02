---
domain: legal
module: matter-management
feature: confidential-access
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Confidential Access

A second access layer on top of CompanyScope: confidential matters are visible only to their owner and named users.

## Behaviour

- `is_confidential` + `access_list` (user ids) on the matter.
- All reads flow through `MatterService::accessibleFor(User)` — the single API. Confidential matters are excluded unless the user is the owner or in `access_list`, **even for `view-any` holders**.
- Legal spend inherits this scope (expenses on a confidential matter are hidden the same way).

## UI

- **Kind**: simple-resource — surfaced as a confidentiality section within `MatterResource` (toggle + user multiselect), plus a confidential badge on the list; not a standalone screen.
- **Page**: confidentiality panel on the matter form (`/legal/matters/{id}/edit`).
- **Layout**: toggle "Confidential" → reveals an access-list user multiselect; list rows show a lock badge.
- **Key interactions**: toggle confidential; add/remove users from access list; non-listed users never see the row.
- **States**: empty (no access list → owner-only) · loading · error (cannot remove last owner) · selected (locked row badge).
- **Gating**: editing confidentiality requires `legal.matters.update` **and** already having access to the matter.

## Data

- Owns / writes: `legal_matters` (`is_confidential`, `access_list`).
- Reads: `users` for the access-list picker (platform).
- Cross-domain writes: none — legal.spend reads the scope, it is not written there ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `accessibleFor` scope consumed by legal.spend for expense visibility.
- Shared entity: `users` (platform).

## Test Checklist

### Unit
- [ ] `accessibleFor` builder excludes confidential matters when user is neither owner nor in `access_list`
- [ ] Owner and `access_list` members are always included regardless of `view-any`

### Feature (Pest)
- [ ] `view-any` holder NOT on the access list cannot see/open a confidential matter (list + direct fetch)
- [ ] Owner sees their confidential matter; adding a user to `access_list` grants visibility
- [ ] `legal.spend` expense visibility inherits the same scope (confidential-matter expenses hidden)

### Livewire
- [ ] Confidentiality toggle reveals the access-list multiselect; lock badge shows on restricted list rows
- [ ] Editing confidentiality requires `legal.matters.update` AND existing access to the matter

## Unknowns

- `*(assumed)*` `view-any` never bypasses confidential scope — [[../unknowns]].

## Related

- [[../_module|Matter Management]] · [[../security]] · [[./matter-records]]
