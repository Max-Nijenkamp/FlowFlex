---
domain: hr
module: compensation-benefits
feature: compensation-bands
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Compensation Bands

## Purpose

Define min/mid/max salary per job grade, per department, and compare bands against actual employee salaries (compa-ratio).

## Behavior (intended)

- Pay band builder: set min/mid/max per role; unique on `(company_id, job_grade, department_id)`; `department_id` null = company-wide.
- Cross-field validation `min ≤ mid ≤ max` ("Band values must satisfy min ≤ mid ≤ max.").
- Compa-ratio = employee salary vs band midpoint, computed via `brick/money`; `null` when no matching band.
- Band-vs-actual comparison shown at band level, not exact salaries.
- Amounts stored as integer cents (`bigint`).

## Tables

- `hr_compensation_bands`

## Permissions

- `hr.compensation.view-any`, `hr.compensation.manage-bands`

## UI

- **Kind**: simple-resource
- **Page**: "Compensation Bands" (`/hr/compensation-bands`)
- **Layout**: Filament table (job grade, department, min/mid/max, currency) + band-builder form; a compa-ratio column/comparison shows band-level (not exact salaries)
- **Key interactions**: HR sets min/mid/max per grade/department; cross-field validation `min ≤ mid ≤ max`; reviews compa-ratio of employees vs midpoint
- **States**: empty (no bands → "Define your first pay band") · loading (table skeleton) · error ("Band values must satisfy min ≤ mid ≤ max." / duplicate `(grade, department)`) · selected (row edit form)
- **Gating**: visible with `hr.compensation.view-any`; create/edit requires `hr.compensation.manage-bands`

## Data

- Owns / writes: `hr_compensation_bands`
- Reads: employee salaries via [[salary-history|payroll profile]] for compa-ratio (band-level only) — own domain
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none
- Shared entity: `department` and employees (hr.profiles) for band scoping and compa-ratio

## Test Checklist

### Unit
- [ ] Cross-field rule rejects `min > mid` and `mid > max` with the "min ≤ mid ≤ max" message
- [ ] Compa-ratio = salary ÷ midpoint via `brick/money` (no float); returns `null` with no matching band

### Feature (Pest)
- [ ] Duplicate `(company_id, job_grade, department_id)` rejected; `department_id` null = company-wide band
- [ ] Compa-ratio comparison exposes band level only, never exact salaries
- [ ] Company A cannot see or edit company B bands

### Livewire
- [ ] Create/edit denied without `hr.compensation.manage-bands`; resource hidden when `hr.compensation` inactive
- [ ] Band-builder form surfaces the min ≤ mid ≤ max and duplicate errors

## Related

- [[../_module]]
- [[../data-model]]
- [[../api]]
