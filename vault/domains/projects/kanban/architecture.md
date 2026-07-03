---
domain: projects
module: kanban
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Kanban — Architecture

## Services & Actions

- `KanbanService::board(string $projectId, BoardFilterData $filters, string $groupBy): BoardData` — single query + in-memory grouping (no N+1).
- Moves delegate to `MoveTask` (owned by projects.tasks) — Kanban never writes tasks directly.

## Events

- `TaskMoved` (`ShouldBroadcast`) on `company.{id}.projects` carrying `task_id, from, to, moved_by` — collaborative UI sync only, not a cross-domain domain event ([[../../../architecture/websockets]]).

## Filament Artifacts

**Nav group:** Projects

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `KanbanBoardPage` | #3 Kanban custom page | [[../../../architecture/patterns/page-blueprints#Kanban]] | Livewire + Alpine SortableJS; Reverb broadcast + presence; project selector; slide-over detail; optimistic card move |

**Access contract (mandatory):** `KanbanBoardPage` gates on
`canAccess() = Auth::user()->can('projects.kanban.view') && BillingService::hasModule('projects.kanban')`
per [[../../../architecture/filament-patterns]] #1. It is a custom page and MUST state this explicitly — Filament
does not auto-gate custom pages. Card mutations additionally require `projects.tasks.update` (enforced inside
`MoveTask`, which owns the write).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Card move / reorder (via `MoveTask`) | Optimistic | Delegates to projects.tasks `MoveTask` — card moves on drop, action runs after, board re-renders from server on exception ([[../../../architecture/patterns/optimistic-locking]]); collaborative sync via `TaskMoved` broadcast |
| Board read (`KanbanService::board`) | n/a | Read-only derived view — Kanban owns no tables and holds no writable state |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## No Tables

Kanban is a pure view. See [[data-model]]. All persistence lives in projects.tasks.

## Search & Realtime

Reverb broadcast for live card moves. No search.
