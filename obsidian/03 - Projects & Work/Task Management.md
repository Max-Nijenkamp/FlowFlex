---
tags: [flowflex, domain/projects, tasks, kanban, phase/2]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: complete
last_updated: 2026-05-07
---

# Task Management

The foundation of all project work. Tasks can exist independently or within projects. Multiple views for different working styles.

**Who uses it:** All employees
**Filament Panel:** `projects`
**Depends on:** Core
**Phase:** 2
**Build complexity:** High — 3 resources, 4 pages, 6 tables

## Implementation (Phase 2 — Built)

**Filament Resources:**
- `TaskResource` — nav group: Tasks, sort: 1
- `TaskLabelResource` — nav group: Tasks, sort: 2

**Models:** `Task`, `TaskLabel`, `TaskDependency`, `TaskAutomation`, `TaskAutomationLog`
Pivot table: `task_label_assignments` (many-to-many Task ↔ TaskLabel)

**Events wired:**
- `TaskAssigned` → `NotifyAssigneeTaskAssigned` → `TaskAssignedNotification` to assignee tenant

**What's live:**
- Task form: title, description, priority enum (P1–P4), status enum (todo/in_progress/done etc.), assignee (tenant select scoped to company), due_date, start_date, estimated_hours
- Label multi-select with `belongsToMany` via `task_label_assignments`
- Task table: title, status badge (with colour), priority badge (with colour), assignee email, due_date (red if overdue), estimated_hours
- Filter by status and priority
- Default sort: `created_at DESC`

**Permissions enforced:** `projects.tasks.*`, `projects.labels.*`

**Not yet built (future phases):** Kanban board view, calendar view, timeline view, subtasks, recurring tasks, task automations UI, dependency visualization

## Events Fired

- `TaskCreated`
- `TaskCompleted` → consumed by [[Project Planning]] (updates project progress), [[Invoicing]] (if milestone-linked)
- `TaskOverdue`
- `TaskAssigned`

## Views

| View | Description |
|---|---|
| Kanban board | Drag cards between custom status columns |
| List view | Dense, sortable, filterable |
| Calendar view | Tasks plotted on a calendar by due date |
| Timeline view | Lightweight Gantt for individual task scheduling |
| My Work view | Personal inbox — all tasks assigned to me, across all projects |

Grouping options: by assignee, by priority, by label, by project.

## Task Properties

| Property | Description |
|---|---|
| Title, description | Rich text description |
| Assignee | Single or multiple |
| Due date and start date | — |
| Priority | P1 Critical / P2 High / P3 Medium / P4 Low — or custom labels |
| Custom status columns | Per board/project |
| Labels and tags | Free-form, colour-coded |
| Estimated hours | — |
| Subtasks | Unlimited nesting depth |
| Dependencies | This task blocks / is blocked by another task |
| Attachments | Files, images |
| Time logged | Links to [[Time Tracking]] module if active |
| Linked records | Link a task to a CRM deal, an employee record, an invoice |

## Task Automations

Rules that trigger automatically based on task events.

**Trigger options:**
- Task created
- Task completed
- Status changed
- Due date reached
- Assignee changed

**Action options:**
- Notify a user
- Change a field
- Create a subtask
- Assign to someone
- Move to a status
- Create an invoice (if [[Finance]] active)

Additional features:
- Automation log (history of all triggered automations)
- Enable/disable automations per project

## Recurring Tasks

- Recurrence options: daily, weekly, bi-weekly, monthly, quarterly, annually, custom
- Recurrence end: never, after N occurrences, on a date
- New instance created automatically when previous completes (or on schedule)
- Recurrence pausing

## Database Tables (6)

1. `tasks` — core task records
2. `task_subtasks` — subtask relationships (parent_id self-reference)
3. `task_dependencies` — blocking/blocked-by pairs
4. `task_labels` — label definitions per workspace
5. `task_automations` — automation rule definitions
6. `task_automation_logs` — trigger history

## Related

- [[Projects Overview]]
- [[Project Planning]]
- [[Time Tracking]]
- [[Team Collaboration]]
- [[Agile & Sprint Management]]
- [[Resource & Capacity Planning]]
