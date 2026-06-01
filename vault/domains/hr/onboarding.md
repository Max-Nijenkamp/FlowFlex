---
type: module
domain: HR & People
panel: hr
module-key: hr.onboarding
status: planned
color: "#4ADE80"
---

# Onboarding

Structured onboarding for new hires — task checklists, document collection, equipment requests, and a self-service portal experience. Triggered automatically when a new employee is created.

---

## Core Features

- Onboarding template: reusable task list (checklist items per department/role)
- Task types: HR task, IT task, manager task, employee self-service task
- Auto-trigger: when an employee's hire date arrives, onboarding plan activates
- Document collection: request signed documents from employee (contract, ID, tax forms)
- Equipment request: laptop, phone, access cards — routed to IT
- Onboarding progress dashboard: HR can see all active onboardings, % complete
- Welcome email sent automatically on activation with self-service portal link
- 30/60/90 day milestone check-in reminders

---

## Data Model

| Table | Key Columns |
|---|---|
| `hr_onboarding_templates` | company_id, name, description, department_id (nullable) |
| `hr_onboarding_tasks` | template_id, company_id, title, description, assigned_role (hr/it/manager/employee), order |
| `hr_onboarding_plans` | company_id, employee_id, template_id, started_at, completed_at |
| `hr_onboarding_plan_tasks` | plan_id, task_id, company_id, status (pending/complete/skipped), completed_by, completed_at |

---

## Filament

**Nav group:** Employees

- `OnboardingResource` — list active onboardings; click into plan to see task progress
- `OnboardingTemplateResource` — create/edit templates and task lists
- Task completion tracked via Livewire actions on the plan detail page

---

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/employee-self-service]]
