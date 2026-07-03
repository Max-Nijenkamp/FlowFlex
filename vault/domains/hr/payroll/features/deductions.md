---
domain: hr
module: payroll
feature: deductions
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Deductions & Employer Cost

## Purpose
Configurable per-company deduction types and employer cost reporting.

## Intended Behavior
- Deduction types are company-configured: `calculation_type` percent or flat; `value` = basis points for percent *(assumed)*, cents for flat.
- `is_employer_contribution` distinguishes employee deductions from employer contributions.
- Deduction math (percent + flat) is exact via `brick/money` — no float drift.
- Employer cost report: total gross payroll + employer contributions per run (`total_employer_cost_cents`).
- v1 has NO statutory tax engine — flat/percent types only *(assumed — see [[../unknowns]])*.

## Tables / Permissions / Events
- Tables: `hr_deduction_types`, `hr_payroll_runs` ([[../data-model]])
- Permissions: `hr.payroll.manage-deductions`

## UI

- **Kind**: simple-resource
- **Page**: "Deduction Types" (`/hr/deduction-types`)
- **Layout**: standard Filament table (name, calculation_type, value, employer-contribution badge) + create/edit form; employer-cost totals surfaced on the payroll-run detail page rather than here
- **Key interactions**: HR defines percent/flat deduction types, toggles `is_employer_contribution`, and reviews per-run employer cost on the run page
- **States**: empty (no deduction types → prompt to add first type) · loading (table skeleton) · error (validation on value/calculation_type) · selected (row edit form)
- **Gating**: visible with `hr.payroll.view`; managing types requires `hr.payroll.manage-deductions`

## Data

- Owns / writes: `hr_deduction_types`
- Reads: `hr_payroll_runs` (employer-cost totals, read-only) — via [[../architecture|PayrollService]]
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none (deduction math is consumed internally by `GeneratePayslipsJob`)
- Shared entity: none

## Test Checklist

### Unit
- [ ] Percent deduction (basis points) and flat deduction (cents) computed exactly via `brick/money`
- [ ] `is_employer_contribution` splits employee deductions from employer contributions in the totals

### Feature (Pest)
- [ ] Employer-cost total per run = gross payroll + employer contributions (`total_employer_cost_cents`)
- [ ] Company A cannot see or edit company B deduction types

### Livewire
- [ ] `DeductionTypeResource` create/edit denied without `hr.payroll.manage-deductions`
- [ ] Validation rejects an invalid `calculation_type` / negative `value`

Back to [[../_module]].
