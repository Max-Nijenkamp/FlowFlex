---
domain: hr
module: shift-scheduling
feature: coverage-gaps
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Coverage Gaps

## Purpose

Surface shifts with no assigned employee so managers can fill them before publishing.

## Intended Behavior

- `coverageGaps(CarbonImmutable $weekStart): Collection<ShiftData>` returns the week's shifts where `employee_id` is null.
- The calendar page highlights these gaps visually.
- Leave-driven unassignment (see [[features/leave-conflict-blocking]]) can create new gaps that appear here.

## Tables / Permissions / Events

- Tables: `hr_shifts`
- Permissions: `hr.shifts.view-any`
- Events: none fired

## UI

- **Kind**: custom-page highlighting *(rendered on the shift calendar; could also surface as a widget)*
- **Page**: "Shift Schedule" (`/hr/shift-schedule`) — gaps highlighted inline on the calendar
- **Layout**: unassigned shifts (`employee_id` null) render as visually highlighted slots on the roster grid; `coverageGaps(weekStart)` drives the highlight list. Optional summary count for the week.
- **Key interactions**: click a highlighted gap to assign an employee (delegates to [[shift-assignment]]); scan the week for uncovered roles before publishing.
- **States**: empty ("No coverage gaps — week fully staffed") · loading (grid skeleton) · error (inline banner) · selected (gap slot opens the assign flow).
- **Gating**: visible with `hr.shifts.view-any`; filling a gap requires `hr.shifts.update`.

## Data

- Owns / writes: none (read-only view over this module's `hr_shifts` where `employee_id` is null)
- Reads: reads `hr_shifts` within this module; reads `hr_employees` via EmployeeService for assignment candidates
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none directly *(gaps can be created by [[leave-conflict-blocking]] unassigning employees on approved leave)*
- Feeds: none
- Shared entity: `hr_employees` (read via EmployeeService)

## Test Checklist

### Unit
- [ ] `coverageGaps(weekStart)` returns only shifts with a null `employee_id` for that week

### Feature (Pest)
- [ ] Leave-driven unassignment ([[leave-conflict-blocking]]) surfaces new gaps for the week
- [ ] Company A cannot see company B coverage gaps

### Livewire
- [ ] Gaps visible with `hr.shifts.view-any`; filling a gap requires `hr.shifts.update`

## Related

- [[../_module]] · [[../architecture]]
