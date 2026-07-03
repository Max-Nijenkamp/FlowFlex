---
domain: projects
module: gantt
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Gantt — Architecture

## Services & Actions

- `GanttService::data(string $projectId): GanttData` — single query set; **critical path** via longest-path walk over the dependency DAG (acyclic guaranteed by projects.tasks).
- Mutations delegate to `UpdateTaskAction` (due date / estimate) — Gantt never writes tasks directly.

## Critical Path

Longest chain of dependencies by duration. Computed with a topological longest-path over `proj_task_dependencies` (DAG is cycle-free by the tasks module's `DependencyCycleException` guard).

## Filament Artifacts

**Nav group:** Projects

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `GanttChartPage` | #5 Gantt custom page | [[../../../architecture/patterns/page-blueprints#Gantt / Timeline]] | `frappe-gantt` in a Blade partial via Alpine bridge; project selector + zoom in header; polling 60s; JS lib bundled in the panel theme (vite) — no CDN *(assumed)* |

**Access contract (mandatory):** `GanttChartPage` gates on
`canAccess() = Auth::user()->can('projects.gantt.view') && BillingService::hasModule('projects.gantt')`
per [[../../../architecture/filament-patterns]] #1. It is a custom page and MUST state this explicitly — Filament
does not auto-gate custom pages. Drag mutations additionally require `projects.tasks.update` (enforced inside
`UpdateTaskAction`, which owns the write).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Drag reschedule / resize (via `UpdateTaskAction`) | Optimistic | Delegates to projects.tasks `UpdateTaskAction` — `updated_at` stale-check on the task record; a rejected write reverts the bar and toasts ([[../../../architecture/patterns/optimistic-locking]]) |
| Chart read (`GanttService::data`) | n/a | Read-only derived view — Gantt owns no tables and holds no writable state |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None. Uses 60s polling for freshness (not broadcast) *(assumed)*.

## No Tables

Pure view — see [[data-model]].

## Search & Realtime

Polling 60s. No search.
