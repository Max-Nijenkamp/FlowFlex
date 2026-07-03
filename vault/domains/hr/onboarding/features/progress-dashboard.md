---
domain: hr
module: onboarding
feature: progress-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Progress Dashboard

Part of [[../_module]].

## Purpose

Let HR see all active onboardings and their % complete at a glance.

## Behavior

- `OnboardingResource` lists active plans with % complete; view = task checklist with Livewire complete/skip actions.
- `ActiveOnboardingsWidget` (Filament widget) shows count + overdue tasks *(assumed)*.
- % derived from `OnboardingService::progress($planId)`.

## Tables / Permissions / Events

- Tables: `hr_onboarding_plans`, `hr_onboarding_plan_tasks`.
- Permissions: `hr.onboarding.view-any`, `hr.onboarding.view`.
- Events: none.

## UI

- **Kind**: custom-page (dashboard; may also surface widgets)
- **Page**: "Onboarding Dashboard" (`/hr/onboarding`)
- **Layout**: dashboard of all active onboardings — table/cards with employee, template, % complete, days since start; `ActiveOnboardingsWidget` stat cards on top.
- **Key interactions**: scan active onboardings; drill into a plan.
- **States**: empty = "No active onboardings" · loading = skeleton · error = "Could not load" · selected = drill into a plan.
- **Gating**: visible with `hr.onboarding.view`.
- **Companion widget**: `ActiveOnboardingsWidget` (stat cards).

## Data

- Owns / writes: none (read view over `hr_onboarding_plans`/`hr_onboarding_plan_tasks`).
- Reads: `hr_employees` (owned by hr.profiles) for display.
- Cross-domain writes: none.

## Relations

- Consumes: none.
- Feeds: none.
- Shared entity: reads `hr_employees` (owned by hr.profiles).

> "Overdue" depends on unverified due-date semantics — see [[../unknowns]].

## Test Checklist

### Unit
- [ ] Per-plan % complete derives from `OnboardingService::progress`

### Feature (Pest)
- [ ] Dashboard lists only active plans for the current tenant
- [ ] Company A cannot see company B onboardings (tenant isolation)

### Livewire
- [ ] `ActiveOnboardingsWidget` renders the active count and drills into a plan
- [ ] Widget/list hidden without `hr.onboarding.view-any`
