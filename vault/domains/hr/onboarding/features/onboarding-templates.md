---
domain: hr
module: onboarding
feature: onboarding-templates
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Onboarding Templates

Part of [[../_module]].

## Purpose

Reusable task lists (checklist items per department/role) that drive each new hire's onboarding plan.

## Behavior

- A template carries a name, optional description, optional `department_id` (null = company default), and an `is_default` flag (one default per company *(assumed)*).
- Each template owns ordered tasks with an `assigned_role` (hr / it / manager / employee).
- Managed via `OnboardingTemplateResource` (CRUD resource + repeater task editor).
- Created through `CreateOnboardingTemplateData` (min 1 task).

## Tables / Permissions / Events

- Tables: `hr_onboarding_templates`, `hr_onboarding_tasks`.
- Permissions: `hr.onboarding.manage-templates`, `hr.onboarding.create`, `hr.onboarding.update`.
- Events: none.

## UI

- **Kind**: simple-resource (`OnboardingTemplateResource`)
- **Page**: "Onboarding Templates" (`/hr/onboarding-templates`)
- **Layout**: table of templates (name, department, `is_default`, task count) + create/edit form with a repeater/relation for tasks.
- **Key interactions**: create/edit templates; add/reorder tasks in the repeater with an `assigned_role` per task.
- **States**: empty = "No templates yet" + Create CTA · loading = table skeleton · error = validation (duplicate default per company) · selected = edit form.
- **Gating**: visible with `hr.onboarding.view`; create/edit requires `hr.onboarding.manage` *(assumed)*.

## Data

- Owns / writes: `hr_onboarding_templates`, `hr_onboarding_tasks`.
- Reads: `hr_departments` (for department scoping, owned by hr.profiles).
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: none.
- Shared entity: reads `hr_departments` (owned by hr.profiles).

## Test Checklist

### Unit
- [ ] `CreateOnboardingTemplateData` rejects a template with zero tasks
- [ ] One-default-per-company rule flags a second `is_default` template

### Feature (Pest)
- [ ] Create a department-scoped template with ordered tasks; tasks persist with `assigned_role`
- [ ] Setting a second company default is rejected (existing default preserved)
- [ ] Manage denied without `hr.onboarding.manage-templates`

### Livewire
- [ ] Repeater adds/reorders tasks with an `assigned_role` per row
- [ ] `OnboardingTemplateResource` create/edit hidden without `hr.onboarding.manage-templates`
