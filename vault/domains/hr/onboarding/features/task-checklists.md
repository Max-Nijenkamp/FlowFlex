---
domain: hr
module: onboarding
feature: task-checklists
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Task Checklists

Part of [[../_module]].

## Purpose

The per-employee checklist of onboarding tasks, completed or skipped over the course of a plan.

## Behavior

- On plan start, template tasks are materialized into `hr_onboarding_plan_tasks` (status `pending`).
- Task types route by `assigned_role`: HR / IT / manager / employee self-service.
- HR completes/skips tasks from the `OnboardingResource` view (Livewire complete/skip actions); employee-role tasks completed via self-service when active, else HR completes on behalf.
- `OnboardingService::completeTask(CompleteTaskData)` sets status + `completed_by`/`completed_at`; auto-sets plan `completed_at` when the last task closes.
- `progress($planId)` returns % complete/skipped.

## Tables / Permissions / Events

- Tables: `hr_onboarding_plan_tasks`, `hr_onboarding_plans`.
- Permissions: `hr.onboarding.complete-task`, `hr.onboarding.view`.
- Events: none.

## UI

- **Kind**: custom-page (the live per-plan checklist view)
- **Page**: "Onboarding Plan" (`/hr/onboarding/{plan}`)
- **Layout**: per-plan checklist grouped by `assigned_role` (HR/IT/manager/employee), each task with status (pending/complete/skipped) + complete/skip actions.
- **Key interactions**: HR completes or skips a task; employee-role tasks completed via self-service when active.
- **States**: empty = "No tasks in plan" · loading = skeleton · error = "Could not update task" · selected = task row detail.
- **Gating**: visible with `hr.onboarding.view`; complete/skip requires `hr.onboarding.update` *(assumed)*.

> [!warning] UNVERIFIED
> Whether this surface is a standalone custom page or a relation-manager on `OnboardingResource` is not confirmed by the spec.

## Data

- Owns / writes: `hr_onboarding_plan_tasks` (status); reads `hr_onboarding_plans`/`hr_onboarding_tasks`.
- Reads: `hr_employees` (assignee display, owned by hr.profiles).
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: completing the last task sets plan `completed_at` (internal).
- Shared entity: reads `hr_employees` (owned by hr.profiles).

## Test Checklist

### Unit
- [ ] `progress($planId)` returns % of tasks complete or skipped
- [ ] Closing the last remaining task sets plan `completed_at`; a non-final close leaves it null

### Feature (Pest)
- [ ] `completeTask` sets status + `completed_by`/`completed_at`
- [ ] Company A cannot complete a plan task belonging to company B (tenant isolation)
- [ ] Complete denied without `hr.onboarding.complete-task`

### Livewire
- [ ] Complete/skip action on the plan view updates the row and re-computes %
- [ ] Actions hidden without `hr.onboarding.complete-task`
