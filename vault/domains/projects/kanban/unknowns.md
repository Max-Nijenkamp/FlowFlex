---
domain: projects
module: kanban
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Kanban — Unknowns

## Assumed Items

- Group-by default is `section`, with a toggle to `status` *(assumed)*.
- No persisted board config (saved filters / per-user default) in v1 *(assumed)*.

## Open Questions

- Should per-user saved board views (filters, group-by) persist — implying a small `proj_board_prefs` table?
- WIP limits per column — in scope for v1 or later?
- Swimlanes (by assignee/priority) in addition to columns?
