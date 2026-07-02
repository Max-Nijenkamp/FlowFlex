---
domain: projects
module: resource-allocation
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `ResourceAllocationResource` | Settings | #1 CRUD | over-allocation badge |
| `AllocationTimelinePage` | Settings | #5 timeline custom page | users × time allocation bars |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.resources.view-any')
        && BillingService::hasModule('projects.resources');
}
```

## Jobs & Scheduling

None.

## Search & Realtime

None.
