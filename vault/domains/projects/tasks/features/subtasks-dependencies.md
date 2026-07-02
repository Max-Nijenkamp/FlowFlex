---
domain: projects
module: tasks
feature: subtasks-dependencies
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Sub-tasks & Dependencies

Nested sub-tasks and cycle-checked blocks/blocked-by relationships between tasks.

## Behaviour

- Sub-tasks nest unlimited via `parent_task_id`; a sub-task must share the parent's project.
- Dependencies: `blocks` / `related` edges. `AddDependencyAction` rejects self-links and cycles (`DependencyCycleException`).
- The dependency graph is guaranteed acyclic — Gantt critical-path relies on this.

## UI

- **Kind**: simple-resource fragments (relation managers on the task detail page).
- **Page**: "Sub-tasks" and "Dependencies" tabs under the task detail.
- **Layout**: sub-task list with add-inline; dependency picker (task search) + list of blocks/blocked-by with type.
- **Key interactions**: add sub-task inline → optimistic row; add dependency → task picker → cycle validated server-side → error toast on cycle.
- **States**: empty (no sub-tasks/deps) · loading · error (cycle → "This would create a dependency loop") · selected.
- **Gating**: `projects.tasks.update`.

## Data

- Owns / writes: `proj_tasks` (parent_task_id), `proj_task_dependencies`.
- Reads: sibling tasks in the same project.
- Cross-domain writes: none.

## Relations

- Consumes / Feeds: nothing (internal to tasks).
- Shared entity: none.

## Unknowns

- Auto-notify on unblock is an open question *(assumed off)*. See [[../unknowns]].

## Related

- [[../_module|Tasks]] · [[task-crud|Task CRUD]] · [[../../gantt/_module|Gantt]]
