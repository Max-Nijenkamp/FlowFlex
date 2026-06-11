---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.tasks
status: planned
priority: p2
depends-on: [projects.projects, core.billing, core.rbac, core.notifications, core.files]
soft-depends: [projects.kanban, projects.sprints, projects.time, projects.milestones]
fires-events: []
consumes-events: []
patterns: [states]
tables: [proj_tasks, proj_task_sections, proj_task_dependencies, proj_task_comments]
permission-prefix: projects.tasks
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Tasks

Task management within projects: create, assign, prioritise, track status, sub-tasks, dependencies, and comments. Core unit of work in the Projects domain.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/projects\|projects.projects]] | tasks belong to projects |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] + [[domains/core/file-storage\|core.files]] | gating, permissions, @mentions, attachments |
| Soft | kanban / sprints / time / milestones | views and integrations over tasks |

---

## Core Features

- Task record: title, description, status, priority, assignee, due date, estimated hours, project, section
- Task status machine: `todo → in_progress → in_review → done | cancelled`
- Sub-tasks: unlimited nesting (parent_task_id self-referential FK)
- Task dependencies: blocks / blocked-by relationships (cycle-checked)
- Task sections/groups within a project (columns in Kanban, swimlanes)
- Priority levels: urgent, high, medium, low
- Labels/tags via spatie/laravel-tags
- Comments on tasks: threaded discussion (plain text + purified rich text *(assumed: plain v1)*)
- Attachments via Media Library
- @mention notifications to assignee/commenter
- Done blocked while open `blocks` dependencies exist *(assumed: warn-not-block, configurable)*

---

## Data Model

### proj_tasks

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed), project_id FK | ulid | | |
| parent_task_id | ulid | nullable FK self | sub-tasks |
| section_id | ulid | nullable FK | |
| title | string | not null | |
| description | text | nullable | |
| status | string | default `todo` | state machine |
| priority | string | default `medium` | urgent/high/medium/low |
| assignee_id | ulid | nullable FK users | |
| due_date | date | nullable | |
| estimated_hours | decimal(6,2) | nullable | |
| order | int | default 0 | board/section order |
| completed_at | timestamp | nullable | *(assumed)* |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, project_id, status)`, `(company_id, assignee_id, status, due_date)` (My Tasks/workload)

### proj_task_sections — id, project_id FK, company_id, name, order
### proj_task_dependencies — id, task_id FK, depends_on_task_id FK, company_id, type (blocks/related); unique `(task_id, depends_on_task_id)`
### proj_task_comments — id, task_id FK, company_id, user_id FK, body, parent_comment_id nullable, deleted_at

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `todo` | `in_progress` / `cancelled` | assignee or `projects.tasks.update` | |
| `in_progress` | `in_review` / `todo` / `cancelled` | | |
| `in_review` | `done` / `in_progress` | | done sets `completed_at`; milestone progress updated (direct same-domain call) |
| `done` | `in_progress` (reopen) | | clears completed_at |

Audited (lightweight — status only *(assumed)*).

---

## DTOs

### CreateTaskData — project_id (required, member), title (required, max:255), description?, section_id?, parent_task_id? (same project), priority (in set), assignee_id? (project member — "Assignee must be a project member."), due_date?, estimated_hours?, tags[]
### AddDependencyData — task_id, depends_on_task_id (same project, ≠ self, no cycle — `DependencyCycleException`)
### CommentData — task_id, body (required, max:5000), parent_comment_id?

## Services & Actions

Actions:
- `CreateTaskAction` / `UpdateTaskAction`
- `MoveTask::run(taskId, sectionId|status, order)` — board drags route here
- `AddDependencyAction` — cycle check (graph walk)
- `CommentOnTaskAction` — parses @mentions → notifications
- `CompleteTaskAction` — transition + milestone progress update

---

## Filament

**Nav group:** Tasks

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `TaskResource` | #1 CRUD resource | filters: project/assignee/status/priority |
| Task view | #2 detail | comments thread, sub-tasks, dependencies, time log, attachments |
| `MyTasksPage` | #1-style custom page (own scope) | cross-project, grouped by due date |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('projects.tasks.view-any') && BillingService::hasModule('projects.tasks')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rich-text sanitize** (medium): In Core Features/CommentData, state that rich-text comment bodies are sanitized with HTMLPurifier before persistence.
- **Upload contract** (medium): Add an upload constraints note: allowed MIME/type whitelist, max file size, and tenant-scoped companies/{id}/ storage path.

---

## Permissions

`projects.tasks.view-any` · `projects.tasks.view` · `projects.tasks.create` · `projects.tasks.update` · `projects.tasks.delete` · `projects.tasks.comment`

(Project membership additionally scopes everything.)

---

## Test Checklist

- [ ] Tenant isolation + module gating + project-membership scoping
- [ ] Dependency cycle rejected
- [ ] Assignee outside project rejected
- [ ] @mention notifies mentioned user only
- [ ] Done sets completed_at + updates linked milestone progress
- [ ] Sub-task nesting renders; parent must share project
- [ ] MyTasks shows only own incomplete tasks across projects

---

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
app/Filament/Projects/Resources/TaskResource.php
app/Filament/Projects/Pages/MyTasksPage.php
database/factories/Projects/{TaskFactory,TaskSectionFactory,TaskCommentFactory}.php
tests/Feature/Projects/{TaskTest,TaskDependencyTest,TaskCommentTest}.php
```

---

## Related

- [[domains/projects/projects]]
- [[domains/projects/kanban]]
- [[domains/projects/sprints]]
- [[domains/projects/time-tracking]]
