---
domain: projects
module: milestones
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Milestones

Key project checkpoints with target dates. Visible on the Gantt chart and project timeline; trigger notifications when approaching or overdue.

## Module-key

`projects.milestones`

**Priority:** p2  
**Panel:** projects  
**Permission prefix:** `projects.milestones`  
**Tables:** `proj_milestones`, `proj_milestone_tasks`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../projects/_module\|projects.projects]] + [[../tasks/_module\|projects.tasks]] | milestones per project; progress from linked tasks |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, reminders |
| Soft | [[../gantt/_module\|projects.gantt]] | timeline markers |

## Core Features

- Milestone record: title, description, target date, project, status (open/achieved/missed).
- Achieve: mark achieved with actual date + notes.
- Link tasks to a milestone; progress = % of linked tasks complete (updated by task completion — same-domain call).
- Overdue detection: past target + still open → `missed`.
- Cross-project milestone list, filterable by status/date.
- Notification when a milestone is 7 days away and incomplete (once).

## See features/

- [[features/milestone-tracking|Milestone Tracking & Progress]] — record, achieve, task-linked progress.
- [[features/milestone-reminders|Overdue & Reminders]] — scheduled status + 7-day reminder job.

## Build Manifest

```
database/migrations/xxxx_create_proj_milestones_table.php
database/migrations/xxxx_create_proj_milestone_tasks_table.php
app/Models/Projects/{Milestone,MilestoneTask}.php
app/Data/Projects/{CreateMilestoneData,AchieveMilestoneData}.php
app/Actions/Projects/{CreateMilestoneAction,AchieveMilestoneAction,LinkTasksAction}.php
app/Support/Projects/MilestoneProgress.php
app/Console/Commands/Projects/MilestoneStatusCommand.php
app/Filament/Projects/Resources/MilestoneResource.php · Widgets/MilestoneTimelineWidget.php
database/factories/Projects/MilestoneFactory.php
tests/Feature/Projects/MilestoneTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see, edit, or achieve company B's milestones.
- [ ] Module gating: artifacts hidden when `projects.milestones` inactive.
- [ ] Progress % updates when a linked task completes.
- [ ] Cross-project task link rejected.
- [ ] Status command: open past target → missed; 7-day reminder fires once.
- [ ] Achieve stamps date + notes.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `NotificationService::notify` | core.notifications | 7-day reminder to owners |
| Same-domain call | `MilestoneProgress::for` | projects.tasks | `CompleteTaskAction` bumps progress |
| Reads | markers | projects.gantt | timeline display |

**Data ownership:** `projects.milestones` writes only `proj_milestones` + `proj_milestone_tasks`. Reminders go through the notifications service API; progress is a same-domain read helper — no writes to other domains' tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../tasks/_module|Tasks]] · [[../gantt/_module|Gantt]]
- [[../../../glossary]]
