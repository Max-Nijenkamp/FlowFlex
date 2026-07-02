---
domain: hr
module: onboarding
feature: welcome-email
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Welcome Email

Part of [[../_module]].

## Purpose

Send a welcome email automatically when an onboarding plan starts, with a self-service portal link.

## Behavior

- `WelcomeMail` queued on plan start (`notifications` queue) by `OnboardingService::startPlan`.
- Depends on [[../../core/notifications/_module|core.notifications]]. See [[../../../infrastructure/mail]] and [[../../../infrastructure/queue-horizon]].

## Tables / Permissions / Events

- Tables: triggered off `hr_onboarding_plans`.
- Permissions: none (system-sent).
- Events: none.

## UI

- **Kind**: background (`WelcomeMail` queued on plan start)
- **Page**: none — mail job.
- **Layout**: no screen; on onboarding plan start, queue `WelcomeMail` (ShouldQueue) with a self-service portal link.
- **Key interactions**: no UI — triggered by plan start (consumes the `EmployeeHired` chain / plan-created).
- **States**: queued · sending · failed (retry) · sent.
- **Gating**: n/a (system mail via core.notifications).

## Data

- Owns / writes: none (reads plan + employee to compose).
- Reads: `hr_onboarding_plans` (own) + `hr_employees` (owned by hr.profiles).
- Cross-domain writes: none (sends via core.notifications).

## Relations

- Consumes: `EmployeeHired` (via plan start) from hr.profiles → queues `WelcomeMail`.
- Feeds: none.
- Shared entity: reads `hr_employees` (owned by hr.profiles) + core.notifications.
