---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.onboarding
status: planned
priority: v1-core
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [hr.self-service]
fires-events: []
consumes-events: [EmployeeHired]
patterns: [events, email]
tables: [hr_onboarding_templates, hr_onboarding_tasks, hr_onboarding_plans, hr_onboarding_plan_tasks]
permission-prefix: hr.onboarding
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Onboarding

Structured onboarding for new hires — task checklists, document collection, equipment requests, and a self-service portal experience. Triggered automatically when a new employee is created.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | plans attach to employees; trigger = EmployeeHired |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Hard | [[domains/core/notifications\|core.notifications]] | welcome email + milestone reminders |
| Soft | [[domains/hr/employee-self-service\|hr.self-service]] | employee-assigned tasks completed there; without it HR completes on behalf |

---

## Core Features

- Onboarding template: reusable task list (checklist items per department/role)
- Task types: HR task, IT task, manager task, employee self-service task
- Auto-trigger: `EmployeeHired` listener starts the default plan (no-op when no template exists — per [[architecture/event-bus]] contract)
- Document collection: request signed documents from employee (contract, ID, tax forms)
- Equipment request: laptop, phone, access cards — routed to IT (P3: real IT tickets; v1: task type only)
- Onboarding progress dashboard: HR can see all active onboardings, % complete
- Welcome email sent automatically on activation with self-service portal link
- 30/60/90 day milestone check-in reminders

---

## Data Model

### hr_onboarding_templates

| Column | Type | Constraints |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | not null |
| description | text | nullable |
| department_id | ulid | nullable FK — null = company default |
| is_default | boolean | default false — one default per company *(assumed)* |
| deleted_at | timestamp | nullable |

### hr_onboarding_tasks

| Column | Type | Notes |
|---|---|---|
| id, template_id FK, company_id | ulid | |
| title | string | |
| description | text nullable | |
| assigned_role | string | hr / it / manager / employee |
| due_days_after_start | int nullable | *(assumed — relative due dates)* |
| order | int | |

### hr_onboarding_plans

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), employee_id FK, template_id FK | ulid | |
| started_at | timestamp | |
| completed_at | timestamp nullable | set when all tasks done/skipped |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, completed_at)`

### hr_onboarding_plan_tasks

| Column | Type | Notes |
|---|---|---|
| id, plan_id FK, task_id FK, company_id | ulid | |
| status | string default `pending` | pending / complete / skipped |
| completed_by | ulid nullable FK users | |
| completed_at | timestamp nullable | |

---

## DTOs

### CreateOnboardingTemplateData (input)
| Field | Type | Validation |
|---|---|---|
| name | string | required, max:150 |
| department_id | ?string | nullable, ulid in company |
| tasks | array<{title, assigned_role, order, description?, due_days_after_start?}> | min:1; assigned_role in:hr,it,manager,employee |

### CompleteTaskData (input)
| Field | Type | Validation |
|---|---|---|
| plan_task_id | string | required, ulid |
| status | string | in:complete,skipped |

## Services & Actions

Interface→Service: `OnboardingServiceInterface` → `OnboardingService`.

- `startPlan(string $companyId, string $employeeId, ?string $templateId = null): OnboardingPlanData` — picks dept template → default → no-op; sends welcome mail
- `completeTask(CompleteTaskData $data): void` — marks task; auto-completes plan when last task closed
- `progress(string $planId): float`

## Events

### Consumes: EmployeeHired (from hr.profiles)
Listener: `StartOnboardingFlowListener` — queued, `WithCompanyContext`; behavior per [[architecture/event-bus]] contract (default plan if template exists, else no-op).

---

## Filament

**Nav group:** Employees

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `OnboardingResource` | #1 CRUD resource | active plans list w/ % complete; view = task checklist with Livewire complete/skip actions |
| `OnboardingTemplateResource` | #1 CRUD resource | template + repeater task editor |
| `ActiveOnboardingsWidget` | #6 widget | count + overdue tasks *(assumed)* |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('hr.onboarding.view-any') && BillingService::hasModule('hr.onboarding')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`hr.onboarding.view-any` · `hr.onboarding.view` · `hr.onboarding.create` · `hr.onboarding.update` · `hr.onboarding.complete-task` · `hr.onboarding.manage-templates`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `SendMilestoneCheckInsCommand` | notifications | daily 08:00 | 30/60/90d after started_at, WHERE not-yet-sent flag per milestone *(assumed: milestone log in plan jsonb)* |
| `WelcomeMail` | notifications | on plan start | — |

---

## Test Checklist

- [ ] Tenant isolation: plans of company A invisible to company B
- [ ] Module gating verified
- [ ] `EmployeeHired` starts default plan; no template = no-op, no error
- [ ] Department template preferred over company default
- [ ] Completing last task sets `completed_at`
- [ ] Employee-role task completable via self-service (when active)
- [ ] Welcome mail queued on start
- [ ] Milestone reminders fire once per milestone

---

## Build Manifest

```
database/migrations/xxxx_create_hr_onboarding_templates_table.php
database/migrations/xxxx_create_hr_onboarding_tasks_table.php
database/migrations/xxxx_create_hr_onboarding_plans_table.php
database/migrations/xxxx_create_hr_onboarding_plan_tasks_table.php
app/Models/HR/{OnboardingTemplate,OnboardingTask,OnboardingPlan,OnboardingPlanTask}.php
app/Data/HR/{CreateOnboardingTemplateData,CompleteTaskData,OnboardingPlanData}.php
app/Contracts/HR/OnboardingServiceInterface.php
app/Services/HR/OnboardingService.php
app/Listeners/HR/StartOnboardingFlowListener.php
app/Mail/HR/WelcomeMail.php
app/Console/Commands/HR/SendMilestoneCheckInsCommand.php
app/Filament/HR/Resources/{OnboardingResource,OnboardingTemplateResource}.php
app/Filament/HR/Widgets/ActiveOnboardingsWidget.php
database/factories/HR/{OnboardingTemplateFactory,OnboardingPlanFactory}.php
tests/Feature/HR/{OnboardingFlowTest,OnboardingListenerTest}.php
```

---

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/employee-self-service]]
- [[architecture/event-bus]]
