---
domain: projects
module: kanban
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Kanban — Architecture

## Services & Actions

- `KanbanService::board(string $projectId, BoardFilterData $filters, string $groupBy): BoardData` — single query + in-memory grouping (no N+1).
- Moves delegate to `MoveTask` (owned by projects.tasks) — Kanban never writes tasks directly.

## Events

- `TaskMoved` (`ShouldBroadcast`) on `company.{id}.projects` carrying `task_id, from, to, moved_by` — collaborative UI sync only, not a cross-domain domain event ([[../../../architecture/websockets]]).

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `KanbanBoardPage` | Projects | #3 Kanban custom page | Livewire + Alpine SortableJS; Reverb broadcast; project selector; slide-over detail |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.kanban.view')
        && BillingService::hasModule('projects.kanban');
}
```

Card mutations additionally require the relevant `projects.tasks.*` permission (moves go through `MoveTask`).

## No Tables

Kanban is a pure view. See [[data-model]]. All persistence lives in projects.tasks.

## Search & Realtime

Reverb broadcast for live card moves. No search.
