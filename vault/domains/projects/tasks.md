---
type: module
domain: Projects & Work
panel: projects
module-key: projects.tasks
status: planned
color: "#4ADE80"
---

# Tasks

> Task creation, assignment, status tracking, subtasks, comments, and attachments — the atomic unit of all work in FlowFlex Projects.

**Panel:** `projects`
**Module key:** `projects.tasks`

## What It Does

Tasks is the foundation of the Projects domain. Every piece of work — whether inside a sprint, on a kanban board, or on a gantt chart — is represented as a task. Tasks can stand alone or belong to a project, a sprint, or another task as a subtask. They carry a rich set of fields: title, rich-text description, assignees, priority, status, due date, estimates, labels, and attachments. Threaded comments enable discussion. Dependencies (A blocks B) are tracked and surfaced visually in the Gantt view. Notifications fire when tasks are assigned, due, or mentioned in comments.

## Features

### Core
- Task fields: title, rich-text description, assignee(s), due date, start date, priority (urgent/high/medium/low), status, parent task (subtask), project, labels, estimate (hours or points), attachments
- Configurable statuses per project: companies define their own status columns (e.g. To Do, In Progress, In Review, Done)
- Subtasks: unlimited depth nesting via `parent_id` self-referential FK — subtask count and completion shown on parent
- Comments: threaded discussion on each task with @mention support — mentioned users notified
- Attachments: file uploads via file-storage module — link to task

### Advanced
- Task dependencies: A blocks B — blocked tasks visually flagged; Gantt auto-adjusts when blocker is delayed
- Recurring tasks: daily / weekly / monthly / custom — new task auto-created on cadence; previous recurrence archived
- Bulk actions: select multiple tasks → assign, move status, set due date, change project — for sprint planning and project kick-off
- "My Tasks" view: personal filtered view showing tasks assigned to the current user, sorted by due date and priority
- Watchers: employees subscribe to a task to receive updates without being the assignee

### AI-Powered
- Effort estimate suggestions: AI analyses task title and description and suggests a story point or hour estimate based on similar past tasks
- Auto-assignment: based on workload and skills data, AI suggests the best available assignee for a new task

## Data Model

```erDiagram
    proj_tasks {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        ulid parent_id FK
        string title
        longtext description
        ulid assignee_id FK
        string priority
        string status
        date due_date
        date start_date
        decimal estimate_hours
        integer story_points
        integer sort_order
        timestamps created_at/updated_at
        timestamp deleted_at
    }

    proj_task_comments {
        ulid id PK
        ulid task_id FK
        ulid company_id FK
        ulid author_id FK
        text body
        timestamps created_at/updated_at
    }

    proj_task_dependencies {
        ulid task_id FK
        ulid blocks_task_id FK
    }
```

| Column | Notes |
|---|---|
| `priority` | urgent / high / medium / low |
| `parent_id` | Self-referential FK for subtasks |
| `sort_order` | Integer for drag-and-drop ordering within status column |

## Permissions

- `projects.tasks.view`
- `projects.tasks.create`
- `projects.tasks.edit`
- `projects.tasks.delete`
- `projects.tasks.manage-all`

## Filament

- **Resource:** `TaskResource` — list view with filters, search, status column filter
- **Pages:** `ListTasks`, `CreateTask`, `EditTask`, `ViewTask` (with comments, subtasks, attachments tabs)
- **Custom pages:** None (visual views handled by Kanban and Gantt modules)
- **Widgets:** `MyTasksWidget` — current user's overdue and due-today tasks on dashboard
- **Nav group:** Work (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Asana | Task and project management |
| Jira | Issue and task tracking |
| Monday.com | Work item management |
| ClickUp | Task management and tracking |

## Related

- [[sprints]]
- [[kanban]]
- [[gantt]]
- [[milestones]]
- [[time-tracking]]
