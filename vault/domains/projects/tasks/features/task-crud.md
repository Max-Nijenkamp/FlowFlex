---
domain: projects
module: tasks
feature: task-crud
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Task CRUD & Status

The core task record: create, assign, prioritise, and drive through the status machine.

## Behaviour

- Create/edit a task (title, description, priority, assignee, due date, estimate, section).
- Status machine `todo → in_progress → in_review → done | cancelled`; `done` stamps `completed_at` and updates linked milestone progress.
- Assignee must be a project member.

## UI

- **Kind**: simple-resource (list/form) + #2 detail view.
- **Page**: `TaskResource` at `/app/projects/tasks`; detail `/app/projects/tasks/{id}`.
- **Layout**: table (title, project, assignee avatar, status badge, priority chip, due date). Filters: project/assignee/status/priority. Detail = tabs comments / sub-tasks / dependencies / time / attachments.
- **Key interactions**: create modal/form; status transition actions gated by the machine; inline assignee/priority edit.
- **States**: empty (no tasks → CTA) · loading (skeleton) · error (toast) · selected (row → detail).
- **Gating**: `projects.tasks.view-any`/`view`; edits `projects.tasks.update`.

## Data

- Owns / writes: `proj_tasks` (+ `proj_task_sections` for grouping).
- Reads: `users` (assignee picker), project membership (scope).
- Cross-domain writes: none — `done` calls same-domain `MilestoneProgress`, notifications via core.notifications API ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: broadcast `TaskMoved` (view sync); same-domain milestone progress on `done`.
- Shared entity: `users`.

## Test Checklist

### Unit
- [ ] Status machine allows only valid transitions; `done` stamps `completed_at`, reopen clears it.
- [ ] `CreateTaskData` rejects an assignee who is not a project member.

### Feature (Pest)
- [ ] Create → assign → drive `todo → in_progress → in_review → done`; `done` updates linked milestone progress (same-domain call, no cross-domain write).
- [ ] Tenant + project-membership scope: a non-member cannot view or edit a project's tasks; company A cannot touch company B's tasks.
- [ ] Concurrent edit: a stale form save raises `StaleRecordException` and shows the conflict notification (first write survives).

### Livewire
- [ ] `TaskResource` denied without `projects.tasks.view-any`/`view`; hidden when `projects.tasks` inactive.
- [ ] Status transition action gated by `projects.tasks.update`; illegal transition rejected with a toast.

## Unknowns

- Recurring tasks + full-text search deferred *(assumed)*. See [[../unknowns]].

## Related

- [[../_module|Tasks]] · [[subtasks-dependencies|Sub-tasks & Dependencies]] · [[../../milestones/_module|Milestones]]
