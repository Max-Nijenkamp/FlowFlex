---
type: module
domain: HR & People
panel: hr
phase: 2
status: complete
migration_range: 100000–109999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-hr-phase2]]"
---

# Onboarding

Template-driven employee onboarding checklists with task completion tracking.

## Module Key
`hr.onboarding`

## Features
- Reusable onboarding templates with ordered tasks
- Assignee roles per task (hr_manager, it, manager, employee)
- Per-employee checklists generated from templates
- Progress tracking (total, completed, percentage)
- Auto-complete checklist when all required items done
- Due dates calculated from hire date

## Files
- Migrations: `100005` through `100008`
- Models: `OnboardingTemplate`, `OnboardingTemplateTask`, `OnboardingChecklist`, `OnboardingChecklistItem`
- Service: `App\Services\HR\OnboardingService`
- Interface: `App\Contracts\HR\OnboardingServiceInterface`
- Events: `EmployeeOnboardingStarted`, `EmployeeOnboardingCompleted`
- Filament: `OnboardingTemplateResource`, `OnboardingChecklistResource`
- Tests: `tests/Feature/HR/OnboardingServiceTest.php`
