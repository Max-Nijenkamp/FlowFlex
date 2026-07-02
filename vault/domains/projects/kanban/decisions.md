---
domain: projects
module: kanban
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Kanban — Decisions

## ADR: Pure view — owns no tables

- **Context:** A board is a projection of tasks, not a new entity.
- **Decision:** Kanban owns no tables; all state lives in projects.tasks. Moves route through `MoveTask`.
- **Consequences:** Honours data-ownership; no duplicate write path; board always consistent with the task list.

## ADR: Single-query board (no N+1)

- **Decision:** `KanbanService::board` fetches all cards in one query and groups in memory.
- **Consequences:** Board scales to large projects; enforced by an N+1 test.

## ADR: Moves reuse `MoveTask`, broadcast via `TaskMoved`

- **Decision:** Drag → `MoveTask` (validation shared with the task resource); a `ShouldBroadcast` `TaskMoved` syncs other viewers.
- **Consequences:** One validation path; collaborative live updates without cross-domain coupling.
