---
domain: hr
module: shift-scheduling
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Shift Scheduling — Unknowns

Assumptions and open items carried from the source spec. Resolve via ADR before/during rebuild.

## Assumptions (`*(assumed)*`)

- Overnight shifts (end_time before start_time) handled via an `end_next_day` flag on `hr_shifts`. *(assumed)*
- Swap requests use a plain `status` string field with a linear flow (pending → accepted → approved / declined) — no spatie/laravel-model-states. *(assumed)*

## Unverified

- Whole module is `build-status: planned`; nothing implemented, migrated, or tested. Source spec frontmatter said `status: complete` — that was incorrect and is not carried forward.
- Consumed-event payload shape for `LeaveRequestApproved` assumed to match [[../../../architecture/event-bus]]; confirm the leave-range fields the listener needs.
- No explicit rule in spec for what happens to pending swap requests when a shift is cancelled or unassigned by leave — behavior undefined.

## Related

- [[_module]] · [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
