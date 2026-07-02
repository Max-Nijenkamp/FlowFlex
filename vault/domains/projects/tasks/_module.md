---
domain: projects
module: tasks
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Tasks

Task management within projects: create, assign, prioritise, track status, sub-tasks, dependencies, and comments. The core unit of work in the Projects domain.

## Module-key

| Field | Value |
|---|---|
| key | `projects.tasks` |
| priority | p2 |
| panel | projects |
| permission-prefix | `projects.tasks` |
| tables | `proj_tasks`, `proj_task_sections`, `proj_task_dependencies`, `proj_task_comments` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../projects/_module\|projects.projects]] | tasks belong to projects |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] + [[../../core/file-storage/_module\|core.files]] | gating, permissions, @mentions, attachments |
| Soft | kanban / sprints / time / milestones | views + integrations over tasks |

## Core Features

- Task record: title, description, status, priority, assignee, due date, estimated hours, project, section.
- Status machine `todo → in_progress → in_review → done | cancelled`.
- Sub-tasks: unlimited nesting (`parent_task_id` self-FK).
- Dependencies: blocks / blocked-by (cycle-checked graph).
- Sections/groups within a project (Kanban columns / swimlanes).
- Priority: urgent / high / medium / low. Labels via spatie/laravel-tags.
- Threaded comments (rich text purified with HTMLPurifier), attachments via Media Library.
- @mention notifications to assignee/commenter.

## See features/

- [[features/task-crud|Task CRUD & Status]] — the core record + state machine.
- [[features/subtasks-dependencies|Sub-tasks & Dependencies]] — nesting + cycle-checked blockers.
- [[features/comments-mentions|Comments & @mentions]] — threaded discussion + notifications.
- [[features/my-tasks|My Tasks]] — cross-project personal work list.

## Build Manifest

```
database/migrations/xxxx_create_proj_task_sections_table.php
database/migrations/xxxx_create_proj_tasks_table.php
database/migrations/xxxx_create_proj_task_dependencies_table.php
database/migrations/xxxx_create_proj_task_comments_table.php
app/Models/Projects/{Task,TaskSection,TaskDependency,TaskComment}.php
app/States/Projects/Task/{TaskState,Todo,InProgress,InReview,Done,Cancelled}.php
app/Data/Projects/{CreateTaskData,AddDependencyData,CommentData,TaskData}.php
app/Actions/Projects/{CreateTaskAction,UpdateTaskAction,MoveTask,AddDependencyAction,CommentOnTaskAction,CompleteTaskAction}.php
app/Exceptions/Projects/DependencyCycleException.php
app/Filament/Projects/Resources/TaskResource.php · Pages/MyTasksPage.php
database/factories/Projects/{TaskFactory,TaskSectionFactory,TaskCommentFactory}.php
tests/Feature/Projects/{TaskTest,TaskDependencyTest,TaskCommentTest}.php
```

## Test Checklist

- [ ] Tenant isolation + module gating + project-membership scoping.
- [ ] Dependency cycle rejected.
- [ ] Assignee outside project rejected.
- [ ] @mention notifies mentioned user only.
- [ ] Done sets `completed_at` + updates linked milestone progress.
- [ ] Sub-task nesting renders; parent shares project.
- [ ] MyTasks shows only own incomplete tasks across projects.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `NotificationService::notify` | core.notifications | @mention + assignment notifications |
| Reads/Commands | Media Library API | core.files | attachments, tenant-scoped path |
| Same-domain call | `MilestoneProgress::for` | projects.milestones | `CompleteTaskAction` bumps linked milestone progress |
| Broadcast | `TaskMoved` (ShouldBroadcast) | projects.kanban / gantt / workload | UI sync only, not a domain event |

**Data ownership:** `projects.tasks` writes only its four tables. Notifications and files are created through their owning services' APIs; milestone progress is a same-domain read helper — no writes to other domains' tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../projects/_module|Projects]] · [[../kanban/_module|Kanban]] · [[../sprints/_module|Sprints]] · [[../time-tracking/_module|Time Tracking]]
- [[../../../glossary]]
