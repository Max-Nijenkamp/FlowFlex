---
domain: hr
module: employee-self-service
feature: my-onboarding
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# My Onboarding

**Purpose.** Let the employee complete onboarding tasks assigned from their onboarding plan.

**Behavior.** Pending-tasks tile on `SelfServiceDashboardPage`; task completion scoped to the auth employee. Tile and completion render only when `hr.onboarding` is active; hidden otherwise (soft-dep degraded behavior).

**Source module.** [[../../onboarding/_module]] (soft dependency)

**Permissions.** `hr.self-service.view`.

## UI

- **Kind**: custom-page (soft-dep hr.onboarding — page hidden when hr.onboarding inactive)
- **Page**: "My Onboarding" (`/app/my-onboarding`)
- **Layout**: own onboarding plan progress + list of employee-assigned tasks with a complete-task action.
- **Key interactions**: view plan progress; open a task; mark an employee-role task complete.
- **States**: empty = no active plan → tile hidden / "Nothing to onboard"; loading = skeleton; error = "Could not load tasks"; selected = task detail / complete.
- **Gating**: visible with `hr.self-service.access` AND hr.onboarding active; completing an employee-role task requires the onboarding task-complete permission *(assumed)*.

  > [!warning] UNVERIFIED
  > Page/tile is hidden entirely when the hr.onboarding module is inactive (soft-dep degraded behavior).

## Data

- Owns / writes: none — this module owns no tables.
- Reads: own `hr_onboarding_plans` + `hr_onboarding_plan_tasks` (owned by hr.onboarding) scoped to own employee.
- Cross-domain writes: task completion via hr.onboarding's service (never a direct write — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none (renders hr.onboarding plan live via service).
- Feeds: task-complete handled by hr.onboarding (no own event fired).
- Shared entity: reads hr.onboarding plan / tasks.

[[../_module]]
