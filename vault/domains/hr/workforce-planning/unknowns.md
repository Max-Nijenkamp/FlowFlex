---
domain: hr
module: workforce-planning
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workforce Planning — Unknowns

Assumptions and unverified points carried from the source spec. Resolve before/during rebuild.

## Assumptions (`*(assumed)*`)

- Scenario planning uses multiplier presets (best/expected/worst-case), not separate plan rows.
- `hr_headcount_plans.expected_attrition` defaults to `0`.

## Unverified

- No cross-domain events are fired or consumed — confirm no downstream module expects a plan/role signal.
- `period` format validation covers both quarter (`2026-Q3`) and year (`2027`) forms — exact validation rule not specified.

## Related

- [[_module]]
