---
domain: projects
module: sprints
feature: burndown-velocity
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Burndown & Velocity

Sprint burndown chart and rolling velocity metrics.

## Behaviour

- Burndown: remaining story points/hours per sprint day, derived from task completion timestamps (no snapshot table *(assumed)*).
- Velocity: completed points per sprint + rolling 3-sprint average.

## UI

- **Kind**: widget (chart on the sprint view) + read metrics.
- **Page**: `BurndownChartWidget` on `SprintResource` view; velocity shown on the sprint list / dashboard.
- **Layout**: apex line chart (ideal vs actual remaining); velocity bar/number with rolling average.
- **Key interactions**: hover data points → tooltip; date-range implicit to the sprint.
- **States**: empty (sprint not started → "Burndown appears once the sprint is active") · loading (chart skeleton) · error (toast) · selected (n/a).
- **Gating**: `projects.sprints.view-any`.

## Data

- Owns / writes: nothing (read-only computation over own + task data).
- Reads: `proj_sprint_tasks` (points) + `proj_tasks.completed_at` (burndown).
- Cross-domain writes: none.

## Relations

- Consumes / Feeds: nothing.
- Shared entity: `proj_tasks` (completion timestamps).

## Test Checklist

### Unit
- [ ] Burndown remaining-points curve computed correctly from task completion timestamps over a fixture sprint.
- [ ] Velocity rolling 3-sprint average correct across a fixture sequence of completed sprints.

### Feature (Pest)
- [ ] `SprintService::burndown` derives per-day remaining from `proj_tasks.completed_at` with no N+1.
- [ ] `SprintService::velocity` sums completed points per sprint scoped to the project.
- [ ] Tenant isolation: burndown/velocity read only the current company's sprint + task data.

### Livewire
- [ ] `BurndownChartWidget` denied without `projects.sprints.view-any`; hidden when `projects.sprints` inactive.
- [ ] Widget shows the "Burndown appears once the sprint is active" empty state before the sprint starts.

## Unknowns

- Snapshot table for perf/history accuracy; points vs hours mode — see [[../unknowns]].

## Related

- [[../_module|Sprints]] · [[sprint-lifecycle|Sprint Lifecycle]] · [[../../../analytics/_index|Analytics]]
