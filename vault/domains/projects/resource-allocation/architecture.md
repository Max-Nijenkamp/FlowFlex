---
domain: projects
module: resource-allocation
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Resource Allocation — Architecture

## Services & Actions

- `AllocationService::create(CreateAllocationData): AllocationData` — returns `over_allocated: bool` (warn, not reject).
- `AllocationService::utilisation(userId, from, to): array{planned, actual}` — actual from time entries when active.
- `AllocationService::availableCapacity(from, to): Collection` — per-user free % across overlapping allocations.

## Over-allocation model

Overlapping allocations summing >100% raise a **warning flag** in the response, not a rejection *(assumed)* — planners often intentionally over-commit short-term.

## Events

None cross-domain.

## Filament Artifacts

**Nav group:** Settings

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ResourceAllocationResource` | #1 CRUD resource | list filters: team, project, date | computed over-allocation badge column (a warning flag, **not** a model-states badge); create/edit/delete gated on `projects.resources.manage` |
| `AllocationTimelinePage` | #5 Gantt / Timeline custom page | [[../../../architecture/patterns/page-blueprints#Gantt / Timeline]] | users (rows) × time (columns) allocation bars coloured per project; over-allocated cells flagged red; date-range + team filters in header; polling 60s *(assumed)* |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.resources.view-any') && BillingService::hasModule('projects.resources')`
per [[../../../architecture/filament-patterns]] #1. `AllocationTimelinePage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. Create/edit/delete additionally require
`projects.resources.manage`.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Allocation edit (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Allocation create + over-allocation guard (`AllocationService::create`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` over the user's overlapping allocations to sum against 100% for a consistent `over_allocated` warning (warn, not block) per [[../../../architecture/patterns/states]] |
| Timeline / capacity read (`availableCapacity`, `utilisation`) | n/a | Read-only derived view — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None.

## Search & Realtime

None.
