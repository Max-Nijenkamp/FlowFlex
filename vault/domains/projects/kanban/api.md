---
domain: projects
module: kanban
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Kanban — API / DTOs

## Input

### BoardFilterData
`assignee_id?`, `label?`, `priority?`, `due_before?` — optional board filters. `groupBy` param: `section` | `status`.

Card moves reuse **projects.tasks** `MoveTask` (`taskId, sectionId|status, order`) — Kanban defines no move DTO of its own.

## Output

### BoardData
`columns[]` (id, name, task_count), `cards[]` (task summary fields). Single-query build.

## Public / Portal Endpoints

None.
