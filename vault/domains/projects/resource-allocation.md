---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.resources
status: planned
priority: p2
depends-on: [projects.projects, core.billing, core.rbac]
soft-depends: [projects.time, projects.workload]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [proj_resource_allocations]
permission-prefix: projects.resources
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Resource Allocation

Allocate team members to projects by percentage of their time. Plan capacity across multiple concurrent projects.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/projects\|projects.projects]] | allocations per project |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/projects/time-tracking\|projects.time]] | allocation-vs-actual comparison |
| Soft | [[domains/projects/workload\|projects.workload]] | overlay |

---

## Core Features

- Allocation record: user, project, percentage of time, start date, end date
- Per-user total allocation: sum across overlapping date ranges (warn >100% — warn, not block *(assumed)*)
- Allocation timeline: Gantt-style view of who is on which project when
- Capacity planning: forecast available capacity for new projects
- Allocation vs actual: compare planned % against logged time (from Time Tracking)
- Filter by team, project, date range
- Conflict detection: flag over-allocated members (>100% across projects)

---

## Data Model

### proj_resource_allocations

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| user_id | ulid | not null FK | |
| project_id | ulid | not null FK | |
| allocation_percent | int | 1–100 | |
| start_date / end_date | date | end ≥ start | |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, user_id, start_date, end_date)`

---

## DTOs

### CreateAllocationData — user_id, project_id, allocation_percent (1–100), start_date/end_date (end ≥ start); over-100% across overlapping ranges = warning flag in response, not rejection

## Services & Actions

- `AllocationService::create(CreateAllocationData $data): AllocationData` — returns `over_allocated: bool`
- `AllocationService::utilisation(string $userId, CarbonImmutable $from, CarbonImmutable $to): array{planned: float, actual: float}` — actual from time entries when active
- `AllocationService::availableCapacity(CarbonImmutable $from, CarbonImmutable $to): Collection` — per user free %

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ResourceAllocationResource` | #1 CRUD resource | over-allocation badge |
| `AllocationTimelinePage` | #5 timeline custom page | users × time allocation bars |

---

## Permissions

`projects.resources.view-any` · `projects.resources.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Overlapping allocations sum correctly; >100% flagged
- [ ] Planned vs actual uses time entries when module active, omitted otherwise
- [ ] Available capacity math over fixtures
- [ ] End before start rejected

---

## Build Manifest

```
database/migrations/xxxx_create_proj_resource_allocations_table.php
app/Models/Projects/ResourceAllocation.php
app/Data/Projects/{CreateAllocationData,AllocationData}.php
app/Services/Projects/AllocationService.php
app/Filament/Projects/Resources/ResourceAllocationResource.php
app/Filament/Projects/Pages/AllocationTimelinePage.php
database/factories/Projects/ResourceAllocationFactory.php
tests/Feature/Projects/ResourceAllocationTest.php
```

---

## Related

- [[domains/projects/workload]]
- [[domains/projects/time-tracking]]
- [[domains/projects/projects]]
