---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.milestones
status: planned
priority: p2
depends-on: [projects.projects, projects.tasks, core.billing, core.rbac, core.notifications]
soft-depends: [projects.gantt]
fires-events: []
consumes-events: []
patterns: []
tables: [proj_milestones, proj_milestone_tasks]
permission-prefix: projects.milestones
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Milestones

Key project checkpoints with target dates. Visible on the Gantt chart and project timeline. Trigger notifications when approaching or overdue.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/projects\|projects.projects]] + [[domains/projects/tasks\|projects.tasks]] | milestones per project; progress from linked tasks |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, reminders |
| Soft | [[domains/projects/gantt\|projects.gantt]] | timeline markers |

---

## Core Features

- Milestone record: title, description, target date, project, status (open/achieved/missed)
- Milestone achievement: mark as achieved with actual date + notes
- Link tasks to a milestone (task completion contributes to milestone progress)
- Milestone progress: % of linked tasks complete (updated by task completion — same-domain direct call)
- Overdue detection: target date passed and status still open → `missed`
- Milestone view: list of all milestones across projects, filterable by status and date
- Notification when milestone is 7 days away and incomplete (once)

---

## Data Model

### proj_milestones

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), project_id FK | ulid | |
| title | string | |
| description | text nullable | |
| target_date | date | |
| achieved_date | date nullable | |
| status | string default `open` | open / achieved / missed (plain enum — trivial transitions, no spatie states *(assumed)*) |
| notes | text nullable | achievement notes |
| reminded_at | timestamp nullable | 7-day once-guard |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status, target_date)`

### proj_milestone_tasks — id, milestone_id FK, task_id FK, company_id; unique `(milestone_id, task_id)`

---

## DTOs

### CreateMilestoneData — project_id, title (required), description?, target_date (required), task_ids[] (same project)
### AchieveMilestoneData — milestone_id, achieved_date (≤ today), notes?

## Services & Actions

Actions:
- `CreateMilestoneAction` / `AchieveMilestoneAction`
- `LinkTasksAction::run(milestoneId, taskIds)` — same-project check
- `MilestoneProgress::for(milestoneId): float` — done/total linked tasks (called by `CompleteTaskAction`)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `MilestoneStatusCommand` | notifications | daily 07:30 | marks open→missed past target; 7-day reminders with `reminded_at` guard |

---

## Filament

**Nav group:** Projects

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MilestoneResource` | #1 CRUD resource | achieve action, progress column, cross-project list |
| Milestone timeline widget | #6 widget | on project view page |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('projects.milestones.view-any') && BillingService::hasModule('projects.milestones')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`projects.milestones.view-any` · `projects.milestones.create` · `projects.milestones.update` · `projects.milestones.achieve`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Progress % updates when linked task completes
- [ ] Cross-project task link rejected
- [ ] Status command: open past target → missed; 7-day reminder fires once
- [ ] Achieve stamps date + notes

---

## Build Manifest

```
database/migrations/xxxx_create_proj_milestones_table.php
database/migrations/xxxx_create_proj_milestone_tasks_table.php
app/Models/Projects/{Milestone,MilestoneTask}.php
app/Data/Projects/{CreateMilestoneData,AchieveMilestoneData}.php
app/Actions/Projects/{CreateMilestoneAction,AchieveMilestoneAction,LinkTasksAction}.php
app/Support/Projects/MilestoneProgress.php
app/Console/Commands/Projects/MilestoneStatusCommand.php
app/Filament/Projects/Resources/MilestoneResource.php
app/Filament/Projects/Widgets/MilestoneTimelineWidget.php
database/factories/Projects/MilestoneFactory.php
tests/Feature/Projects/MilestoneTest.php
```

---

## Related

- [[domains/projects/tasks]]
- [[domains/projects/gantt]]
