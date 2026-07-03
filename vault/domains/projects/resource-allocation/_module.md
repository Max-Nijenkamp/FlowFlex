---
domain: projects
module: resource-allocation
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Resource Allocation

Allocate team members to projects by percentage of their time; plan capacity across concurrent projects.

## Module-key

`projects.resources`

**Priority:** p2  
**Panel:** projects  
**Permission prefix:** `projects.resources`  
**Tables:** `proj_resource_allocations`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../projects/_module\|projects.projects]] | allocations per project |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../time-tracking/_module\|projects.time]] | allocation-vs-actual comparison |
| Soft | [[../workload/_module\|projects.workload]] | overlay |

## Core Features

- Allocation record: user, project, % of time, start/end date.
- Per-user total across overlapping ranges (warn >100% — warn, not block *(assumed)*).
- Allocation timeline: who is on which project when (Gantt-style).
- Capacity planning: forecast free capacity for new projects.
- Allocation vs actual: planned % vs logged time.
- Conflict detection: flag over-allocated members.

## See features/

- [[features/allocation-record|Allocation Record & Conflicts]] — CRUD + over-allocation warning.
- [[features/capacity-timeline|Capacity Timeline]] — who's on what, when + free-capacity forecast.

## Build Manifest

```
database/migrations/xxxx_create_proj_resource_allocations_table.php
app/Models/Projects/ResourceAllocation.php
app/Data/Projects/{CreateAllocationData,AllocationData}.php
app/Services/Projects/AllocationService.php
app/Filament/Projects/Resources/ResourceAllocationResource.php · Pages/AllocationTimelinePage.php
database/factories/Projects/ResourceAllocationFactory.php
tests/Feature/Projects/ResourceAllocationTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot view or edit company B's allocations.
- [ ] Module gating: artifacts hidden when `projects.resources` inactive.
- [ ] Overlapping allocations sum correctly; >100% flagged.
- [ ] Planned vs actual uses time entries when module active, omitted otherwise.
- [ ] Available capacity math over fixtures.
- [ ] End before start rejected.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | logged time (actual %) | projects.time | `utilisation()`; omitted when time module inactive |
| Reads | overlay | projects.workload | combined capacity view |

**Data ownership:** `projects.resources` writes only `proj_resource_allocations`. Actual utilisation reads time entries read-only; nothing writes into other domains' tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../workload/_module|Workload]] · [[../time-tracking/_module|Time Tracking]] · [[../projects/_module|Projects]]
- [[../../../glossary]]
