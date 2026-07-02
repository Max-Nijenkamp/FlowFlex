---
domain: projects
module: gantt
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Gantt — Architecture

## Services & Actions

- `GanttService::data(string $projectId): GanttData` — single query set; **critical path** via longest-path walk over the dependency DAG (acyclic guaranteed by projects.tasks).
- Mutations delegate to `UpdateTaskAction` (due date / estimate) — Gantt never writes tasks directly.

## Critical Path

Longest chain of dependencies by duration. Computed with a topological longest-path over `proj_task_dependencies` (DAG is cycle-free by the tasks module's `DependencyCycleException` guard).

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `GanttChartPage` | Projects | #5 Gantt custom page | `frappe-gantt` in a Blade partial via Alpine bridge; project selector + zoom in header; polling 60s |

JS lib bundled in the panel theme (vite) — no CDN *(assumed)*.

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.gantt.view')
        && BillingService::hasModule('projects.gantt');
}
```

Drag mutations additionally require `projects.tasks.update`.

## Events

None. Uses 60s polling for freshness (not broadcast) *(assumed)*.

## No Tables

Pure view — see [[data-model]].

## Search & Realtime

Polling 60s. No search.
