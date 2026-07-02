---
domain: projects
module: milestones
feature: milestone-tracking
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Milestone Tracking & Progress

Create milestones, link tasks, track completion progress, and mark achieved.

## Behaviour

- Create a milestone (title, target date, linked tasks in the same project).
- Progress = % of linked tasks complete; updated when a linked task completes (same-domain call).
- Achieve → stamp `achieved_date` + notes.

## UI

- **Kind**: simple-resource (cross-project list/form) + a #6 timeline widget on the project view.
- **Page**: `MilestoneResource` at `/app/projects/milestones`; `MilestoneTimelineWidget` on the project detail.
- **Layout**: table (title, project, target date, status chip, progress bar). Achieve = row action modal (date + notes). Widget = horizontal timeline of markers.
- **Key interactions**: create + link tasks (multi-select same project); achieve action; progress bar auto-updates.
- **States**: empty (no milestones → CTA) · loading · error (cross-project link → "Tasks must belong to this project") · selected (row).
- **Gating**: `projects.milestones.view-any`; create/link `create`; achieve `achieve`.

## Data

- Owns / writes: `proj_milestones`, `proj_milestone_tasks`.
- Reads: `proj_tasks` (linked-task completion) via projects.tasks.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: same-domain `MilestoneProgress` call on task completion.
- Feeds: markers to projects.gantt (read).
- Shared entity: `proj_tasks`.

## Unknowns

- Achievement cross-domain event; reopen after date push — see [[../unknowns]].

## Related

- [[../_module|Milestones]] · [[milestone-reminders|Reminders]] · [[../../gantt/_module|Gantt]]
