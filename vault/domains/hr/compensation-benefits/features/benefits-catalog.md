---
domain: hr
module: compensation-benefits
feature: benefits-catalog
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Benefits Catalog

## Purpose

Define the available benefits a company offers (health insurance, pension, gym, lunch).

## Behavior (intended)

- Each benefit has a `type` (insurance / pension / allowance), employee `cost_per_month_cents`, and `employer_contribution_cents`.
- Amounts stored as integer cents (`bigint`).
- Soft-deletable (`deleted_at`).
- Managed via `BenefitResource` (CRUD) in the Payroll nav group.

## Tables

- `hr_benefits`

## Permissions

- `hr.compensation.view-any`, `hr.compensation.manage-benefits`

## UI

- **Kind**: simple-resource
- **Page**: "Benefits" (`/hr/benefits`) — Payroll nav group
- **Layout**: Filament table (name, type badge, employee cost, employer contribution) + create/edit form; soft-deletable
- **Key interactions**: HR defines benefits (insurance/pension/allowance) with `cost_per_month_cents` and `employer_contribution_cents`
- **States**: empty (no benefits → "Add your first benefit") · loading (table skeleton) · error (form validation on type/amounts) · selected (row edit form)
- **Gating**: visible with `hr.compensation.view-any`; create/edit requires `hr.compensation.manage-benefits`

## Data

- Owns / writes: `hr_benefits`
- Reads: none (self-contained catalog)
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none — catalog entries referenced by [[benefit-enrollment]] and read by payroll at run time *(assumed)*
- Shared entity: none

## Related

- [[../_module]]
- [[benefit-enrollment]]
- [[../data-model]]
