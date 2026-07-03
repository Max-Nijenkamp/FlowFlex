---
domain: projects
module: projects
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Projects — Architecture

## State Machine

```
planning → active → on_hold → completed | cancelled
              └──────────────→ completed | cancelled
```

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `planning` | `active` | `projects.projects.update` | |
| `active` | `on_hold` / `completed` / `cancelled` | `projects.projects.update` (complete requires all tasks done or explicit confirm *(assumed: confirm modal)*) | `completed` stamps `completed_at` |
| `on_hold` | `active` / `cancelled` | | |

Audited (spatie/laravel-model-states). See [[../../../architecture/patterns/states]].

## Services & Actions

Interface→Service: `ProjectServiceInterface` → `ProjectService` ([[../../../architecture/patterns/interface-service]]).

| Method | Responsibility |
|---|---|
| `create(CreateProjectData): ProjectData` | creator auto-added as `owner` member |
| `health(string $projectId): string` | completion vs elapsed math (on-track/at-risk/off-track) |
| `actuals(string $projectId): array{hours, cost_cents}` | from time entries; 0 when time module inactive |
| `addMember / removeMember` | membership management |

## Events

None fired or consumed in v1. Health/actuals are read via same-domain and soft-dep read APIs, not events.

## Filament Artifacts

**Nav group:** Projects

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ProjectResource` | #1 CRUD resource | tweaks: state-badge-column (status machine), view-page-tabs | member-scoped listing; list filters: status, owner |
| Project view page | #2 detail with tabs | tweaks: view-page-tabs | Overview, Tasks, Sprints, Milestones, Files, Time (soft-dep tabs shown only when those modules active) |
| `ProjectStatsWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | active count + health pie; `canView()`-guarded; polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.projects.view-any') && BillingService::hasModule('projects.projects')`
per [[../../../architecture/filament-patterns]] #1. The widget additionally `canView()`-guards. Listing is
member-scoped in `ProjectService` (a user without `view-any` sees only projects they belong to) — see [[security]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Project CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Member add / remove / role change (relation manager) | Optimistic | `updated_at` stale-check on the member row ([[../../../architecture/patterns/optimistic-locking]]) |
| Status transition (`planning → active → on_hold → completed \| cancelled`) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write per [[../../../architecture/patterns/states]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None.

## Search & Realtime

None specified.
