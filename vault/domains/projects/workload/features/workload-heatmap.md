---
domain: projects
module: workload
feature: workload-heatmap
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Workload Heat-map

Members × days grid of task load, colour-coded by capacity, with drag-to-rebalance.

## Behaviour

- Cell = sum of `estimated_minutes` of a member's `todo`/`in_progress` tasks due that day.
- Colour by % of daily capacity: green <80%, amber 80–100%, red >100% *(assumed)*.
- Drag a task cell to another member/day → reassign/reschedule via `UpdateTaskAction`.

## UI

- **Kind**: custom-page (heat-map — [[../../../../architecture/patterns/feature-ui-spec]] custom-page kind).
- **Page**: `WorkloadPage` at `/app/projects/workload` (nav group Projects).
- **Layout**: sticky user column + scrollable day/week columns; coloured cells; filters (project/team/date) in header; 60s poll.
- **Key interactions**: drag task between cells → reassign/reschedule; hover cell → task list tooltip; toggle day/week granularity.
- **States**: empty (no assigned tasks in range) · loading (skeleton grid) · error (reassign rejected → revert + toast) · selected (cell highlighted) · overloaded (red cell + alert).
- **Gating**: `projects.workload.view`; drag requires `projects.tasks.update`.

## Data

- Owns / writes: none — reassign/reschedule mutate `proj_tasks` **only** via `UpdateTaskAction`.
- Reads: `proj_tasks` (aggregate) + HR capacity (default 8h/day) + allocation overlay.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing (mutations flow into projects.tasks).
- Shared entity: `proj_tasks` (projects.tasks), HR capacity (hr.profiles), `proj_resource_allocations` (projects.resources).

## Test Checklist

### Unit
- [ ] Cell total sums only `todo`/`in_progress` task `estimated_minutes` bucketed on the due date.
- [ ] Colour level boundaries correct at 80% and 100% of daily capacity (green / amber / red).

### Feature (Pest)
- [ ] `WorkloadService::grid` builds the members × days grid in one aggregate query (no N+1) for a fixture.
- [ ] Capacity resolves from HR profile when `hr.profiles` active; falls back to 8h/day default otherwise.
- [ ] Non-member of the project cannot load `WorkloadService::grid` (membership + tenant scope).
- [ ] Drag reassign routes through `UpdateTaskAction` (task `updated_at` advances; `projects.tasks.update` enforced; stale write rejected).

### Livewire
- [ ] `WorkloadPage` denied without `projects.workload.view`; hidden when `projects.workload` inactive.
- [ ] Rejected reassign reverts the cell and shows an error toast (no silent write).

## Unknowns

- Start→due spreading vs due-date bucketing; leave-aware capacity — see [[../unknowns]].

## Related

- [[../_module|Workload]] · [[../../tasks/_module|Tasks]] · [[../../resource-allocation/_module|Resource Allocation]]
