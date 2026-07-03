---
domain: hr
module: workforce-planning
feature: headcount-plans
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Headcount Plans

## Purpose

Set a target headcount per department per period (quarter or year), against a budget.

## Behavior

- One plan per `(company_id, department_id, period)`; duplicates rejected.
- `department_id` null = company-wide plan.
- Carries `target_headcount`, `expected_attrition`, `budgeted_cost_cents`, `currency`.
- CRUD via `HeadcountPlanResource` (#1 CRUD resource).

## Tables / Permissions

- Table: `hr_headcount_plans` ([[../data-model]])
- Permissions: `hr.workforce.view-any`, `hr.workforce.create`, `hr.workforce.update`

## UI

- **Kind**: simple-resource
- **Page**: "Headcount Plans" (`/hr/headcount-plans`)
- **Layout**: standard Filament `HeadcountPlanResource` table (department, period, target headcount, budgeted cost, currency) with create/edit form; `department_id` null renders as "Company-wide"
- **Key interactions**: create a plan per department+period, edit targets/budget, soft-delete; duplicate `(company, department, period)` rejected on save
- **States**: empty = "No headcount plans yet — create one" · loading = table skeleton · error = validation error on duplicate period / negative headcount · selected = row opens edit form
- **Gating**: visible with `hr.workforce.view-any`; create requires `hr.workforce.create`; edit requires `hr.workforce.update`

## Data

- Owns / writes: `hr_headcount_plans`
- Reads: department reference data from `hr.profiles` (department list) *(assumed)*
- Cross-domain writes: none — never writes another domain's tables ([[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none (parent record; planned roles hang off it)
- Shared entity: `hr_employees` / department reference (read-only, for department selection)

## Test Checklist

### Unit
- [ ] One plan per `(company_id, department_id, period)`; duplicate rejected
- [ ] `department_id` null renders as a company-wide plan; negative `target_headcount` rejected

### Feature (Pest)
- [ ] Creating a duplicate `(company, department, period)` plan is rejected on save
- [ ] Company A cannot see or edit company B headcount plans

### Livewire
- [ ] Create requires `hr.workforce.create`; edit requires `hr.workforce.update`; visible with `hr.workforce.view-any`

## Related

- [[../_module]]
