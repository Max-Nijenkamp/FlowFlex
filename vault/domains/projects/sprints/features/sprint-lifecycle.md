---
domain: projects
module: sprints
feature: sprint-lifecycle
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Sprint Lifecycle & Backlog

Plan, start, and complete sprints; manage the backlog and assign tasks.

## Behaviour

- Create a sprint (name, dates, goal); status `planning → active → completed`.
- Start: one active sprint per project (`ActiveSprintExistsException`).
- Backlog = project tasks not in a sprint; drag into the active sprint (task in one active sprint max).
- Complete: incomplete tasks → backlog or next sprint (user choice); velocity recorded; retro captured.

## UI

- **Kind**: simple-resource (sprint CRUD) + a #3 custom-page board for planning.
- **Page**: `SprintResource` at `/app/projects/sprints`; `SprintBoardPage` at `/app/projects/sprints/board`.
- **Layout**: resource list with start/complete actions + retro form on view. Board = active-sprint columns + backlog sidebar (drag in/out).
- **Key interactions**: start (validates one-active) → confirm; drag backlog task in; complete → modal choosing incomplete-task destination.
- **States**: empty (no sprints → CTA) · loading · error (second active → "This project already has an active sprint") · selected (sprint row / dragged card).
- **Gating**: `projects.sprints.view-any`; mutate `projects.sprints.manage`; assign `projects.sprints.assign-tasks`.

## Data

- Owns / writes: `proj_sprints`, `proj_sprint_tasks`.
- Reads: `proj_tasks` (backlog, completion) via projects.tasks.
- Cross-domain writes: none — board moves reuse projects.tasks' `MoveTask` ([[../../../../security/data-ownership]]).

## Relations

- Consumes / Feeds: nothing (no cross-domain events).
- Shared entity: `proj_tasks` (owned by projects.tasks).

## Test Checklist

### Unit
- [ ] `CompleteSprintData` accepts `incomplete_action` in {backlog, next-sprint}; `next_sprint_id` required when next-sprint.
- [ ] `AssignTaskData` rejects a task from another project or already in another active sprint.

### Feature (Pest)
- [ ] Start rejects a second active sprint per project (`ActiveSprintExistsException`).
- [ ] Complete moves incomplete tasks to backlog vs next sprint per user choice; velocity recorded.
- [ ] Concurrency: two concurrent starts on one project leave exactly one active sprint (pessimistic lock).
- [ ] Tenant isolation: company A cannot start/assign into company B's sprint.

### Livewire
- [ ] `SprintResource` start/complete actions hidden without `projects.sprints.manage`; backlog assign gated by `projects.sprints.assign-tasks`.
- [ ] `SprintBoardPage` denied without `projects.sprints.view-any`; hidden when `projects.sprints` inactive.

## Unknowns

- Cross-project sprints; story-point granularity — see [[../unknowns]].

## Related

- [[../_module|Sprints]] · [[burndown-velocity|Burndown & Velocity]] · [[../../kanban/_module|Kanban]]
