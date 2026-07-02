---
domain: hr
module: onboarding
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Onboarding — API, Events & DTOs

No public REST endpoints. Surfaces are Filament resources (see [[security]]). Interaction is event-driven plus Livewire complete/skip actions on the plan view.

## Events

**Fires:** none.

**Consumes:** `EmployeeHired` (from [[../employee-profiles/_module|hr.profiles]]).
- Listener `StartOnboardingFlowListener`, queued, `WithCompanyContext`.
- Contract per [[../../../architecture/event-bus]]: default plan if template exists, else no-op, no error.

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

### OnboardingPlanData (output)

Returned by `OnboardingService::startPlan`. Represents the created plan (employee, template, started_at, plan tasks).
