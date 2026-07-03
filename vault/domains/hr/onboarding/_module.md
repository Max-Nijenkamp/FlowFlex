---
domain: hr
module: onboarding
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Onboarding

Structured onboarding for new hires — task checklists, document collection, equipment requests, and a self-service portal experience. Intended to trigger automatically when a new employee is created.

> **Build status: planned.** HR code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. This spec is the rebuild blueprint — nothing here is built, shipped, or tested yet.

---

## Module-key

`hr.onboarding`

**Priority:** v1-core  
**Panel:** hr  
**Permission prefix:** `hr.onboarding`  
**Tables:** `hr_onboarding_templates`, `hr_onboarding_tasks`, `hr_onboarding_plans`, `hr_onboarding_plan_tasks`

---

## Core Features

- Reusable onboarding templates: task checklists per department/role.
- Task types: HR / IT / manager / employee self-service.
- Auto-trigger: an `EmployeeHired` listener starts the default plan (no-op when no template exists).
- Document collection (contract, ID, tax forms) and equipment requests (laptop, phone, access — v1 = task type only; P3 = real IT tickets).
- Progress dashboard for HR (all active onboardings, % complete).
- Welcome email on plan start with self-service portal link.
- 30/60/90 day milestone check-in reminders.

See features: [[features/onboarding-templates]] · [[features/task-checklists]] · [[features/plan-generation-on-hire]] · [[features/document-collection]] · [[features/equipment-requests]] · [[features/progress-dashboard]] · [[features/welcome-email]] · [[features/milestone-checkins]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | plans attach to employees; trigger = EmployeeHired |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | welcome email + milestone reminders |
| Soft | [[../employee-self-service/_module\|hr.self-service]] | employee-assigned tasks completed there; without it HR completes on behalf |

---

## Cross-Domain Edges

| Direction | Event | Counterpart |
|---|---|---|
| Consumes | `EmployeeHired` | hr.profiles → start default/department plan (no-op if no template) |
| Feeds | equipment/asset request event *(P3, soft, UNVERIFIED)* | IT provisioning |
| Feeds | welcome + milestone reminders | core.notifications |
| Fires | — | none confirmed |

Owns `hr_onboarding_templates`, `hr_onboarding_tasks`, `hr_onboarding_plans`, `hr_onboarding_plan_tasks`; only `OnboardingService` writes them; reacts to `EmployeeHired` by writing ONLY its own tables ([[../../../security/data-ownership]]).

---

## Notes in this Folder

- [[architecture]] — services, actions, plan-generation + task-completion flow
- [[data-model]] — tables, columns, ERD
- [[api]] — events, DTOs, consumed contracts
- [[security]] — permissions, authz, tenancy
- [[unknowns]] — assumptions + open items

### Features

- [[features/onboarding-templates]]
- [[features/task-checklists]]
- [[features/plan-generation-on-hire]]
- [[features/document-collection]]
- [[features/equipment-requests]]
- [[features/progress-dashboard]]
- [[features/welcome-email]]
- [[features/milestone-checkins]]

### Siblings

- [[../employee-profiles/_module]]
- [[../employee-self-service/_module]]

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

## Test Checklist

- [ ] Tenant isolation: plans of company A invisible to company B
- [ ] Module gating: artifacts hidden when `hr.onboarding` inactive
- [ ] `EmployeeHired` starts default plan; no template = no-op, no error
- [ ] Department template preferred over company default
- [ ] Completing last task sets `completed_at`
- [ ] Employee-role task completable via self-service (when active)
- [ ] Welcome mail queued on start
- [ ] Milestone reminders fire once per milestone

Per-feature detail lives in each `features/*.md` Test Checklist.

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[unknowns]]
- [[../employee-profiles/_module]] · [[../employee-self-service/_module]]
- [[../../core/notifications/_module]]
- [[../../../architecture/event-bus]] · [[../../../architecture/ui-strategy]]
