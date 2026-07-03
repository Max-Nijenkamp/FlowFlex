---
domain: support
module: canned-responses
feature: response-templates
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Response Templates

Author and organise reusable reply templates, personal or team-wide.

## Behaviour

- Template: title, `/shortcut` code, rich-text body, category, `is_shared`, `usage_count`.
- Shortcut unique per company (`[a-z0-9-]+`).
- Personal templates visible to owner only; shared require `support.canned.manage-shared`.
- Search by title/content within scope.

## UI

- **Kind**: simple-resource — `CannedResponseResource` CRUD with shared/personal tabs.
- **Page**: `CannedResponseResource` (`/support/canned-responses`).
- **Layout**: list (title, shortcut, category, shared badge, usage count) with Personal / Shared tabs; form = title + shortcut + body + category + shared toggle.
- **Key interactions**: create/edit; toggle shared (gated); duplicate shortcut rejected inline.
- **States**: empty (no templates → "create your first" CTA) · loading (save) · error (duplicate shortcut) · selected (tab active, row editing).
- **Gating**: view `support.canned.view-any`; create `support.canned.create`; update `support.canned.update`; shared `support.canned.manage-shared`.

## Data

- Owns / writes: `sup_canned_responses`.
- Reads: none cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: [[./composer-insertion]] renders these into ticket/chat replies.
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Shortcut validation: `[a-z0-9-]+`, unique per company; duplicate rejected
- [ ] Rich-text body purified before store (HTMLPurifier)

### Feature (Pest)
- [ ] Personal template (`is_shared=false`) is invisible to another agent in the same company
- [ ] Creating/editing a shared template requires `support.canned.manage-shared`
- [ ] Tenant isolation: company A never lists company B templates

### Livewire
- [ ] Personal/Shared list tabs filter correctly; usage column renders
- [ ] Shared toggle denied without `support.canned.manage-shared`; duplicate shortcut shows inline error

## Unknowns

- Extensible token set per company — [[../unknowns]].

## Related

- [[../_module|Canned Responses]] · [[./composer-insertion]]
