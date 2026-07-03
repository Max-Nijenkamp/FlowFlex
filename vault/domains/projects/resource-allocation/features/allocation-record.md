---
domain: projects
module: resource-allocation
feature: allocation-record
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Allocation Record & Conflicts

Allocate a person to a project at a % over a date range; flag over-allocation.

## Behaviour

- Create/edit an allocation (user, project, %, start/end; end ≥ start).
- On save, sum overlapping allocations per user; >100% raises `over_allocated` warning (not a block).

## UI

- **Kind**: simple-resource (allocation CRUD).
- **Page**: `ResourceAllocationResource` at `/app/projects/resources` (nav group Settings).
- **Layout**: table (user, project, %, date range, over-allocation badge). Filters: team/project/date.
- **Key interactions**: create/edit form; over-allocation badge with tooltip listing conflicting allocations.
- **States**: empty (no allocations → CTA) · loading · error (end < start → toast) · selected (row) · warning (over-allocated badge).
- **Gating**: `projects.resources.view-any`; mutate `projects.resources.manage`.

## Data

- Owns / writes: `proj_resource_allocations`.
- Reads: `users`, `proj_projects`.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes / Feeds: nothing.
- Shared entity: `users`, `proj_projects`.

## Test Checklist

### Unit
- [ ] Overlapping allocations per user sum correctly; a sum >100% sets the `over_allocated` warning flag.
- [ ] `end_date` before `start_date` fails validation.

### Feature (Pest)
- [ ] Create allocation returns `over_allocated: true` when the overlapping sum exceeds 100% (warn, not reject).
- [ ] Mutating an allocation requires `projects.resources.manage`; company A cannot edit company B's allocations (tenant scope).
- [ ] Concurrent creates for the same user get a consistent over-allocation warning under `lockForUpdate`.

### Livewire
- [ ] `ResourceAllocationResource` denied without `projects.resources.view-any`; hidden when `projects.resources` inactive.
- [ ] Over-allocation badge renders with a tooltip listing the conflicting allocations.

## Unknowns

- HR working-time/leave integration for true availability — see [[../unknowns]].

## Related

- [[../_module|Resource Allocation]] · [[capacity-timeline|Capacity Timeline]] · [[../../workload/_module|Workload]]
