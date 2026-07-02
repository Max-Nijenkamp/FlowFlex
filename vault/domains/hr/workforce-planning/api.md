---
domain: hr
module: workforce-planning
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workforce Planning — API

DTOs and service signatures. No cross-domain events fired or consumed. Intended contracts; not yet built (see [[_module]]).

## DTOs (spatie/laravel-data)

### CreateHeadcountPlanData

- `department_id` — nullable
- `period` — required, format-validated (`2026-Q3` / `2027`)
- `target_headcount` — min:0
- `budgeted_cost_cents` — integer minor units
- `expected_attrition` — int

### CreatePlannedRoleData

- `plan_id`
- `title` — required
- `target_start_date`
- `budgeted_salary_cents` — min:0, integer minor units

## Services & Actions

| Signature | Purpose |
|---|---|
| `WorkforceService::planVsActual(string $period): Collection` | per-department target vs current active headcount |
| `ApprovePlannedRoleAction::run(string $roleId): void` | status → approved; creates + links requisition when recruitment active |
| `MarkRoleFilledAction::run(string $roleId): void` | status → filled |

## Related

- [[architecture]]
- [[data-model]]
