---
domain: projects
module: tasks
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Tasks — Decisions

## ADR: Dependency cycles rejected via graph walk

- **Context:** Tasks can block each other; a cycle would break Gantt critical-path and scheduling.
- **Decision:** `AddDependencyAction` walks the dependency graph and throws `DependencyCycleException` if the new edge would create a cycle.
- **Consequences:** The dependency DAG is guaranteed acyclic → Gantt/critical-path can assume it.

## ADR: Done warns (not blocks) on open blockers *(assumed)*

- **Decision:** Marking a task `done` while it has open `blocks` dependencies warns rather than hard-blocks; configurable per company *(assumed)*.
- **Consequences:** Flexible for teams that treat dependencies as advisory; documented as assumed ([[unknowns]]).

## ADR: Milestone progress via same-domain call, not an event

- **Context:** Completing a task should update linked milestone progress.
- **Decision:** `CompleteTaskAction` calls `MilestoneProgress::for()` directly (same Projects domain), not a cross-domain event.
- **Consequences:** Simple + synchronous; only valid because milestones share the Projects bounded context.

## ADR: `TaskMoved` is broadcast-only, not a domain event

- **Decision:** Board drags emit a `ShouldBroadcast` `TaskMoved` for live UI sync; it is not a queued cross-domain domain event.
- **Consequences:** Kanban/Gantt/Workload views stay in sync without coupling other domains.
