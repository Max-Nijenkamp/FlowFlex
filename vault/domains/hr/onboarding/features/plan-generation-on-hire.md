---
domain: hr
module: onboarding
feature: plan-generation-on-hire
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Plan Generation on Hire

Part of [[../_module]].

## Purpose

Automatically start an onboarding plan for a new employee, with no manual step.

## Behavior

- `StartOnboardingFlowListener` consumes `EmployeeHired` (queued, `WithCompanyContext`).
- Delegates to `OnboardingService::startPlan(companyId, employeeId, ?templateId)`.
- Template selection: department template → company default → no-op (no error) when none exists. Contract per [[../../../architecture/event-bus]].
- On success: creates the plan, materializes plan tasks, queues the welcome mail.

## Tables / Permissions / Events

- Tables: `hr_onboarding_plans`, `hr_onboarding_plan_tasks`, `hr_onboarding_templates`, `hr_onboarding_tasks`.
- Permissions: gated server-side; no direct user permission (event-driven).
- Events: consumes `EmployeeHired`; fires none.

## UI

- **Kind**: background (`StartOnboardingFlowListener` on `EmployeeHired`)
- **Page**: none — listener.
- **Layout**: no screen; on `EmployeeHired` the listener instantiates the default (or department) template into a plan + materializes plan_tasks.
- **Key interactions**: no UI — triggered by `EmployeeHired`.
- **States**: queued (listener enqueued) · running · failed (retry) · succeeded (plan created); no-op when no template exists (no error).
- **Gating**: n/a (system listener, runs under `WithCompanyContext`).

## Data

- Owns / writes: `hr_onboarding_plans`, `hr_onboarding_plan_tasks` (materialized from template).
- Reads: `hr_onboarding_templates`/`hr_onboarding_tasks` (own) + `hr_employees`/`hr_departments` (from `EmployeeHired` payload / hr.profiles).
- Cross-domain writes: via events only — writes ONLY its own tables in reaction to the event ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `EmployeeHired` from hr.profiles → starts default/department plan (no-op if no template).
- Feeds: plan start triggers welcome-email + may fire a plan-started effect *(assumed)*.
- Shared entity: reads `hr_employees`, `hr_departments` (owned by hr.profiles).

> [!warning] UNVERIFIED
> A dedicated plan-started event is assumed, not confirmed by the spec.

## Test Checklist

### Unit
- [ ] Template selection precedence: department template → company default → none
- [ ] No matching template returns a no-op (no plan, no exception)

### Feature (Pest)
- [ ] `EmployeeHired` starts a plan, materializes plan tasks, and queues `WelcomeMail`
- [ ] Listener runs under `WithCompanyContext` — plan is written to the hire's company only
- [ ] Duplicate `EmployeeHired` does not create a second plan for the same employee *(assumed guard)*
