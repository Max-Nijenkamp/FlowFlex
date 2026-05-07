---
tags: [flowflex, domain/hr, onboarding, phase/2]
domain: HR & People
panel: hr
color: "#7C3AED"
status: complete
last_updated: 2026-05-07
---

# Onboarding

Structured, templated journeys for every new hire. The goal is a consistent, professional day-one experience without HR manually coordinating every step.

**Who uses it:** HR team, hiring managers, new employees
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]]
**Phase:** 2
**Build complexity:** High — 3 resources, 2 pages, 6 tables

## Implementation (Phase 2 — Built)

**Filament Resources:**
- `OnboardingTemplateResource` — nav group: Onboarding, sort: 1, with `TemplateTasksRelationManager`
- `OnboardingFlowResource` — nav group: Onboarding, sort: 2, eager-loads employee

**Models:** `OnboardingTemplate`, `OnboardingTemplateTask`, `OnboardingFlow`, `OnboardingTask`, `OnboardingCheckin`, `OnboardingCheckinResponse`

**Events wired:**
- `OnboardingStarted` → `NotifyEmployeeOnboardingStarted` (queued, sends `OnboardingStartedNotification`)
- `OnboardingTaskCompleted` → `NotifyHrOnboardingTaskCompleted` (queued)

**What's live:**
- Template builder with active/inactive flag
- Template tasks via relation manager (name, description, assignee role, due-day-offset, is_required)
- Onboarding flow creation: link employee to template, set status (`not_started` → `in_progress` → `completed`)
- Flow table shows employee name (eager-loaded), status badge, started_at timestamp

**Permissions enforced:** `hr.onboarding-templates.*`, `hr.onboarding-flows.*`

**Not yet built (future phases):** pre-boarding portal, 30/60/90-day check-in UI, task progress tracking UI, auto-start from `CandidateHired` event

## Events Fired

- `OnboardingStarted`
- `OnboardingTaskCompleted`
- `OnboardingCompleted` → consumed by [[HR Compliance]] (marks induction complete), LMS (triggers first compliance cert assignments)

## Events Consumed

- `CandidateHired` (from [[Recruitment & ATS]]) → auto-starts onboarding flow

## Sub-modules

### Pre-boarding Portal

The new hire experience **before** their first day. They receive an invite link to a branded portal where they can complete setup tasks without needing a full platform account yet.

**Features:**
- Branded welcome page (company name, their manager's name, start date)
- Document collection (upload ID, right-to-work documents)
- Contract e-signature (pulled from Document Approvals module)
- Personal details form (so HR doesn't have to chase this on day one)
- "What to expect on day one" content block
- Access to company handbook (link to Knowledge Base if active)

### Onboarding Templates

**Features:**
- Template builder (define a sequence of tasks by role/department)
- Task types: document upload, form fill, training course (links to LMS), read and acknowledge, external link
- Default assignees per task (HR, IT, hiring manager)
- Due dates relative to start date (e.g. "Day 1", "Week 1", "Day 30")
- Template cloning and versioning
- Multiple templates (one per job family: Engineering, Sales, Operations, etc.)

### Onboarding Task Management

**Features:**
- Task checklist view for HR team (all active onboardings in one view)
- New hire's personal task list (what they need to do)
- Hiring manager task list (what the manager needs to do for their new hire)
- Task completion notifications (to HR when new hire completes a task)
- Progress bar per new hire (% complete)
- Overdue task alerts

### 30/60/90-Day Check-ins

**Features:**
- Automated check-in form sent to new hire and manager at 30, 60, 90 days
- Configurable questions per check-in milestone
- Manager response and review
- Flag for HR attention if check-in scores below threshold
- Check-in history stored on employee profile

## Cross-module Integrations

When both modules are active:
- **LMS** — onboarding templates can include course assignments
- **IT** — onboarding templates can include IT provisioning checklist tasks
- **Document Approvals** — pre-boarding contract e-signature
- **Asset Management** — equipment request task in onboarding template

## Database Tables (6)

1. `onboarding_flows` — one per employee hire
2. `onboarding_templates` — reusable template definitions
3. `onboarding_template_tasks` — task definitions within a template
4. `onboarding_tasks` — active task instances per employee flow
5. `onboarding_checkins` — 30/60/90-day check-in records
6. `onboarding_checkin_responses` — responses per check-in question

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Recruitment & ATS]]
- [[Offboarding]]
- [[Course Builder & LMS]]
- [[IT Asset Management]]
