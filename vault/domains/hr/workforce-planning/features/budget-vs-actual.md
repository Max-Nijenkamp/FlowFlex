---
domain: hr
module: workforce-planning
feature: budget-vs-actual
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Budget vs Actual

## Purpose

Compare planned headcount and cost against actual active employees and department budget, with scenario views.

## Behavior

- `WorkforceService::planVsActual(period)` returns per-department target vs current active headcount.
- Budget impact: planned headcount × average salary vs department budget; math via `brick/money` on integer minor units.
- Attrition forecast (`expected_attrition`) factored into net headcount.
- Scenario toggle: best/expected/worst-case multiplier presets.
- Budget comparison column depends on soft-dep `finance.budgets`; hidden when unbuilt.
- Surfaced on `WorkforcePlanningDashboard` (#6 dashboard page) with charts and org-growth-over-time visualisation.

## Tables / Permissions

- Tables: `hr_headcount_plans`, `hr_planned_roles` ([[../data-model]])
- Permissions: `hr.workforce.view-any`

## UI

- **Kind**: custom-page
- **Page**: "Workforce Planning" (`/hr/workforce-planning`) — the `WorkforcePlanningDashboard` comparison view
- **Layout**: per-department table of target vs current active headcount, budget-impact column (planned headcount × avg salary vs department budget), a scenario toggle (best/expected/worst), and an org-growth-over-time chart; budget-comparison column hidden when `finance.budgets` inactive
- **Key interactions**: switch scenario preset multiplier; change period; read plan-vs-actual variance; column set adapts to soft-dep availability
- **States**: empty = "No plans for this period" · loading = skeleton table + chart · error = "Couldn't compute plan vs actual" with retry · selected = scenario toggle highlights active preset; hovering a department row shows variance detail · degraded = budget column hidden when `finance.budgets` unbuilt
- **Gating**: visible with `hr.workforce.view-any` and `hr.workforce` module active

## Data

- Owns / writes: none — read-only comparison over `hr_headcount_plans` + `hr_planned_roles` (own module tables)
- Reads: `hr_employees` (actual active headcount) via `hr.profiles`; department budget via `finance.budgets` read API *(assumed)*
- Cross-domain writes: none — never writes another domain's tables ([[../../../../security/data-ownership]])

## Relations

- Consumes: none (reads Finance budget read API directly, `*(assumed)*`; no event subscription)
- Feeds: none (dashboard view only)
- Shared entity: `hr_employees` (read-only headcount), `finance.budgets` department budget (read-only, soft-dep)

## Related

- [[../_module]]
- [[../architecture]]
