---
domain: hr
module: workforce-planning
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Workforce Planning

Headcount planning, hire forecasts, and open role pipeline. Plan future team structure against budget and growth targets.

> Rebuild blueprint. HR code was stripped to the app/admin shell per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing here is built, shipped, or tested — this is the intended design to rebuild against.

---

## Module-key

`hr.workforce`

**Priority:** v1  
**Panel:** hr  
**Permission prefix:** `hr.workforce`  
**Tables:** `hr_headcount_plans`, `hr_planned_roles`

Nav group: Analytics. Encrypted fields: none.

---

## Core Features

- Headcount plan: target headcount per department per period (quarter/year).
- Planned vs actual headcount tracking.
- Hire forecast: planned new roles with target start dates and budgeted cost.
- Open role pipeline: roles approved but not yet filled (links to Recruitment).
- Attrition forecast: expected departures factored into net headcount.
- Budget impact: planned headcount × average salary vs department budget.
- Scenario planning: best/expected/worst-case growth.
- Org growth visualisation over time.

See [[features/headcount-plans]], [[features/planned-roles]], [[features/budget-vs-actual]], [[features/requisition-handoff]].

## Dependencies

| Type | Module | Why | If unbuilt |
|---|---|---|---|
| Hard | [[../employee-profiles/_module]] | actual headcount baseline | blocks plan-vs-actual |
| Hard | core.billing + core.rbac | gating + permissions | blocks all access |
| Soft | [[../recruitment/_module]] | approved planned roles convert to requisitions | status tracked manually |
| Soft | finance.budgets | budget comparison column | column hidden |

## Notes in this folder

- [[architecture]] — services, actions, custom planning page, flow diagram
- [[data-model]] — tables + ERD
- [[api]] — DTOs and service signatures
- [[security]] — permissions, authz, tenancy
- [[unknowns]] — assumptions and open questions

## Features

- [[features/headcount-plans]]
- [[features/planned-roles]]
- [[features/budget-vs-actual]]
- [[features/requisition-handoff]]

## Build Manifest

```
database/migrations/xxxx_create_hr_headcount_plans_table.php
database/migrations/xxxx_create_hr_planned_roles_table.php
app/Models/HR/{HeadcountPlan,PlannedRole}.php
app/Data/HR/{CreateHeadcountPlanData,CreatePlannedRoleData}.php
app/Services/HR/WorkforceService.php
app/Actions/HR/{ApprovePlannedRoleAction,MarkRoleFilledAction}.php
app/Filament/HR/Resources/{HeadcountPlanResource,PlannedRoleResource}.php
app/Filament/HR/Pages/WorkforcePlanningDashboard.php
database/factories/HR/{HeadcountPlanFactory,PlannedRoleFactory}.php
tests/Feature/HR/WorkforcePlanningTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see, edit, or approve company B headcount plans or planned roles
- [ ] Module gating: artifacts hidden when `hr.workforce` inactive
- [ ] `planVsActual` computes per-department target vs current active headcount
- [ ] `ApprovePlannedRoleAction` sets `approved` and, when `hr.recruitment` is active, creates + links one requisition (`requisition_id`); manual status without it
- [ ] Double-approve serialized by `lockForUpdate` — never creates two requisitions
- [ ] `MarkRoleFilledAction` sets `filled`
- [ ] Budget columns are integer minor units via `brick/money`; budget-vs-actual column hidden when `finance.budgets` unbuilt
- [ ] CRUD stale-write raises `StaleRecordException`

## Data Ownership

Owns two tenant-scoped tables: `hr_headcount_plans` and `hr_planned_roles` ([[data-model]]). Reads `hr_employees` (actual headcount) and `finance.budgets` (budget column, soft-dep) read-only; never writes another domain's tables ([[../../../security/data-ownership]]).

## Cross-Domain Edges

| Direction | Event / integration | Counterpart | Effect |
|---|---|---|---|
| Feeds | requisition handoff (direct service call on approve, **not** a fired event) | `hr.recruitment` | approved planned role → recruitment creates a requisition; id stored on `hr_planned_roles.requisition_id` (`workforce -.requisitions.-> recruitment`) |
| Reads | budget read API | `finance.budgets` | budget-vs-actual comparison column *(assumed)*; hidden when unbuilt |
| Reads | headcount read API | `hr.profiles` | actual active headcount baseline for plan-vs-actual |
| Consumes | none | — | no inbound event subscriptions |

## Related

- [[../recruitment/_module]]
- [[../employee-profiles/_module]]
- [[../../../glossary]]
