---
type: module
domain: HR & People
panel: hr
module-key: hr.onboarding
status: planned
color: "#4ADE80"
---

# Onboarding

> Structured onboarding tasks, checklists, document collection, and new-hire welcome sequences — from offer acceptance to productive team member.

**Panel:** `hr`
**Module key:** `hr.onboarding`

## What It Does

Onboarding creates a structured, trackable process for getting new employees productive. When an employee record is created with a future hire date, an onboarding plan can be assigned. The plan is built from a template — a set of tasks, document requests, and introductions assigned to specific people (HR, IT, manager, buddy) with due dates relative to the start date. Progress is tracked per new hire on a Filament kanban-style page. New hires see their tasks via the Employee Self-Service module.

## Features

### Core
- Onboarding templates: reusable task lists per role or department (e.g. "Software Engineer Template" = laptop setup, GitHub access, codebase walkthrough)
- Template tasks: title, description, assignee role (hr / it / manager / buddy / employee), due offset (e.g. "Day -3" before start, "Day 5" after start)
- Plan assignment: HR assigns a template to a new hire when their employee record is created — plan instantiated as concrete tasks with due dates based on `hire_date`
- Task completion: each assigned person marks their tasks complete; HR sees overall plan completion percentage
- Document requests: request specific documents (passport copy, bank details, signed contract) from the new hire via Self-Service

### Advanced
- Automated task creation: `EmployeeHired` event triggers the default onboarding template automatically if company has set a default template
- Welcome email sequence: automated emails at Day -7, Day -1, Day 1, Day 7 with relevant info and task reminders
- Buddy assignment: HR assigns a buddy to the new hire; buddy receives their tasks and an introduction email
- Progress dashboard: Filament page showing all current new hires with hire date, plan completion %, and overdue task count
- Offboarding templates: mirror of onboarding — triggered by `EmployeeTerminated` event for clean exit process

### AI-Powered
- Template suggestions: based on job title and department, AI suggests which onboarding template to use and highlights any tasks that are typically missed for this role
- Completion prediction: flag plans that are at risk of having overdue tasks on Day 1 based on current task completion velocity

## Data Model

```erDiagram
    onboarding_templates {
        ulid id PK
        ulid company_id FK
        string name
        string description
        timestamps created_at/updated_at
    }

    onboarding_template_tasks {
        ulid id PK
        ulid template_id FK
        string title
        text description
        string assignee_role
        integer due_offset_days
        timestamps created_at/updated_at
    }

    onboarding_plans {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid template_id FK
        string status
        timestamp completed_at
        timestamps created_at/updated_at
    }

    onboarding_tasks {
        ulid id PK
        ulid plan_id FK
        ulid company_id FK
        string title
        text description
        ulid assigned_to FK
        date due_date
        boolean is_complete
        timestamp completed_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `due_offset_days` | Negative = before hire date; positive = after |
| `assignee_role` | hr / it / manager / buddy / employee |
| `onboarding_plans.status` | not_started / in_progress / complete |

## Permissions

- `hr.onboarding.view`
- `hr.onboarding.manage-templates`
- `hr.onboarding.assign-plan`
- `hr.onboarding.complete-task`
- `hr.onboarding.view-all-plans`

## Filament

- **Resource:** `OnboardingTemplateResource`
- **Pages:** `ListOnboardingTemplates`, `CreateOnboardingTemplate`, `EditOnboardingTemplate`
- **Custom pages:** `OnboardingDashboardPage` — kanban view of all active new hire plans with task status
- **Widgets:** `OnboardingProgressWidget` — count of new hires in onboarding with average completion %
- **Nav group:** Employees (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| BambooHR | Onboarding checklists and task management |
| Workday | New hire onboarding workflows |
| Notion | Manual onboarding wiki and task tracking |
| Sapling (Kallidus) | Employee onboarding automation |

## Related

- [[employee-profiles]]
- [[employee-self-service]]
- [[recruitment]]
- [[time-attendance]]
