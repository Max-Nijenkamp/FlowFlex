---
domain: projects
module: tasks
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Tasks — API / DTOs

## Input DTOs

### CreateTaskData

| Field | Type | Rules |
|---|---|---|
| `project_id` | ulid | required, caller is a member |
| `title` | string | required, max:255 |
| `description` | text | nullable, HTMLPurified |
| `section_id` | ulid | nullable, in project |
| `parent_task_id` | ulid | nullable, same project |
| `priority` | enum | urgent/high/medium/low |
| `assignee_id` | ulid | nullable, project member — "Assignee must be a project member." |
| `due_date` | date | nullable |
| `estimated_hours` | decimal | nullable |
| `tags` | string[] | spatie/laravel-tags |

### AddDependencyData
`task_id`, `depends_on_task_id` (same project, ≠ self, no cycle → `DependencyCycleException`), `type` (blocks/related).

### CommentData
`task_id`, `body` (required, max:5000, purified), `parent_comment_id?`.

## Output

### TaskData
`id, title, status, priority, assignee_name, due_date, estimated_hours, project_id, section_id, subtask_count, blocked_by[], comment_count`.

## Public / Portal Endpoints

None in v1.
