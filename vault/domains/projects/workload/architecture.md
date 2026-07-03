---
domain: projects
module: workload
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Projects

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `WorkloadPage` | #18 Heat-map / matrix custom page | [[../../../architecture/patterns/page-blueprints#Heat-map / Matrix]] | Livewire + Alpine/CSS grid (members × days); colour by % capacity; drag-to-reassign; filters in header; polling 60s |

**Access contract (mandatory):** `WorkloadPage` gates on
`canAccess() = Auth::user()->can('projects.workload.view') && BillingService::hasModule('projects.workload')`
per [[../../../architecture/filament-patterns]] #1. It is a custom page and MUST state this explicitly — Filament
does not auto-gate custom pages. Drag mutations additionally require `projects.tasks.update` (enforced inside
`UpdateTaskAction`, which owns the write).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Drag reassign / reschedule (via `UpdateTaskAction`) | Optimistic (delegated) | Delegates to projects.tasks `UpdateTaskAction` — `updated_at` stale-check on the task record; a rejected write reverts the cell and toasts ([[../../../architecture/patterns/optimistic-locking]]) |
| Grid read (`WorkloadService::grid`) | n/a | Read-only derived view — Workload owns no tables and holds no writable state |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## No Tables

Pure view — see [[data-model]].

## Search & Realtime

Polling 60s. No search.
