---
domain: projects
module: workload
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Workload — Architecture

## Services & Actions

- `WorkloadService::grid(from, to, WorkloadFilterData): WorkloadGridData` — single aggregate query (no N+1); buckets task estimated-hours by assignee × day.
- Capacity resolution: HR profile working-time when `hr.profiles` active, else default 8h/day.
- Mutations via `UpdateTaskAction` (reassign/reschedule) — Workload never writes tasks directly.

## Colour model

Cell level by % of daily capacity: green <80%, amber 80–100%, red >100% *(assumed thresholds)*. Only `todo`/`in_progress` tasks counted.

## Events

None.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `WorkloadPage` | Projects | #5-style heat-map custom page | Livewire + Alpine grid; drag-to-reassign; filters in header; polling 60s |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.workload.view')
        && BillingService::hasModule('projects.workload');
}
```

Drag mutations additionally require `projects.tasks.update`.

## No Tables

Pure view — see [[data-model]].

## Search & Realtime

Polling 60s. No search.
