---
domain: projects
module: gantt
feature: timeline-view
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Timeline & Critical Path

The Gantt timeline: task bars, milestone markers, dependency arrows, drag-reschedule, and critical-path highlight.

## Behaviour

- Render task bars on a date axis with milestone markers and dependency arrows.
- Zoom day/week/month; today marker; colour by assignee/priority.
- Drag a bar → reschedule (due date via `UpdateTaskAction`); drag an edge → resize (estimate).
- Highlight the critical path (longest dependency chain by duration).
- Export PNG/PDF (client-side *(assumed)*).

## UI

- **Kind**: custom-page (Gantt — [[../../../../architecture/patterns/feature-ui-spec]] custom-page kind).
- **Page**: `GanttChartPage` at `/app/projects/gantt` (nav group Projects).
- **Layout**: left task list + right timeline canvas (`frappe-gantt` via Alpine bridge); project selector + zoom in header; 60s poll.
- **Key interactions**: drag bar → reschedule; drag edge → resize; hover → tooltip; critical path highlighted.
- **States**: empty (no scheduled tasks → "Add tasks with due dates") · loading (skeleton timeline) · error (reschedule rejected → revert + toast) · selected (bar highlighted).
- **Gating**: `projects.gantt.view`; drag requires `projects.tasks.update`.

## Data

- Owns / writes: none — reschedule/resize mutate `proj_tasks` **only** via `UpdateTaskAction`.
- Reads: `proj_tasks`, `proj_task_dependencies`, `proj_milestones` via `GanttService::data` (single query set).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing (mutations flow into projects.tasks).
- Shared entity: `proj_tasks`, `proj_task_dependencies` (projects.tasks), `proj_milestones` (projects.milestones).

## Test Checklist

### Unit
- [ ] Critical-path longest-path walk returns the correct chain on a branched DAG fixture.
- [ ] Bar start inferred as `due − estimated days` when no explicit start *(assumed)*.

### Feature (Pest)
- [ ] `GanttService::data` returns bars, milestone markers, and dependency edges for a fixture project with no N+1.
- [ ] Drag reschedule routes through `UpdateTaskAction` (task `updated_at` advances; permission `projects.tasks.update` enforced).
- [ ] Non-member of the project cannot load `GanttService::data` (membership + tenant scope).

### Livewire
- [ ] `GanttChartPage` denied without `projects.gantt.view`; hidden when `projects.gantt` inactive.
- [ ] Rejected reschedule reverts the bar and shows an error toast (no silent write).

## Unknowns

- Inferred vs explicit task start; live vs polled refresh; baseline compare — see [[../unknowns]].

## Related

- [[../_module|Gantt]] · [[../../tasks/_module|Tasks]] · [[../../milestones/_module|Milestones]]
