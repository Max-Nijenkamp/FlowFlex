---
domain: projects
module: tasks
feature: my-tasks
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# My Tasks

A personal, cross-project list of the current user's incomplete tasks.

## Behaviour

- Shows the acting user's tasks across every project they belong to, grouped by due date (overdue / today / this week / later).
- Only incomplete tasks (`todo` / `in_progress` / `in_review`).
- Own-scope: always filtered to the current user regardless of `view-any`.

## UI

- **Kind**: custom-page (own-scope list; bespoke grouping).
- **Page**: `MyTasksPage` at `/app/projects/my-tasks` (nav group Tasks).
- **Layout**: grouped sections by due bucket; each row inline status + quick-complete; project chip.
- **Key interactions**: quick status change inline; click → task detail; collapse groups.
- **States**: empty (no open tasks → "You're all caught up") · loading (skeleton groups) · error (toast) · selected (row).
- **Gating**: any authenticated user with `projects.tasks.view` (own tasks) — no `view-any` required.

## Data

- Owns / writes: `proj_tasks` (status quick-edit only).
- Reads: `proj_tasks` filtered to `assignee_id = current user`.
- Cross-domain writes: none.

## Relations

- Consumes / Feeds: nothing.
- Shared entity: `users`.

## Unknowns

- Whether it also surfaces tasks where the user is a watcher/mentioned *(assumed assignee-only)*. See [[../unknowns]].

## Related

- [[../_module|Tasks]] · [[task-crud|Task CRUD]] · [[../../workload/_module|Workload]]
