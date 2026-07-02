---
domain: hr
module: employee-profiles
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Unknowns — Employee Profiles

> Every `*(assumed)*` marker and unverified detail from the source spec. Resolve before / during build; each is an authoritative default until overridden by ADR.

## Assumptions to Confirm

- `birth_year` smallint derived column from `date_of_birth` *(assumed)* — confirm existence + how it's populated/maintained.
- `termination_reason` column on `hr_employees` *(assumed)* — confirm type/length vs the DTO's `max:1000`.
- `hr_departments.name` unique `(company_id, name)` *(assumed)* — confirm uniqueness constraint.
- `active → on_leave` may be auto-triggered from approved long leave, but assumed manual for v1 *(assumed: manual v1)*.
- `active → suspended` disables portal login *(assumed)* — confirm the mechanism (user_id flag? guard check?).
- `create_user_account` bool defaults false and triggers an invitation *(assumed)* — confirm invitation flow.
- `hire()` assigns next `employee_number` under an advisory lock per company *(assumed)* — confirm locking strategy.
- `hr.employees.view-sensitive` permission gates encrypted field display *(assumed)* — confirm it exists in the permission seed.
- `DepartmentResource` rendered as simple list in v1, tree deferred *(assumed: simple list v1)*.

## Unverified

- `UpdateEmployeeData` DTO field list — spec names the file (Build Manifest + `update()` signature) but provides no field table. Field set unverified; see [[api]].

## Open Questions

The source spec has no explicit `## Open Questions` section. The items above stand in for the design-affecting unknowns.

Back to [[_module]].
