---
domain: projects
module: gantt
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Gantt — Decisions

## ADR: Pure view — owns no tables

- **Decision:** Gantt reads tasks/dependencies/milestones; mutations go through `UpdateTaskAction`. No Gantt tables.
- **Consequences:** Data-ownership honoured; timeline always reflects task truth.

## ADR: Critical path relies on the acyclic dependency DAG

- **Context:** Longest-path critical-path only works on a DAG.
- **Decision:** Depend on projects.tasks' cycle guard (`DependencyCycleException`); Gantt assumes acyclic input.
- **Consequences:** Simpler algorithm; a cycle would be a tasks-module bug, not a Gantt one.

## ADR: 60s polling, not broadcast *(assumed)*

- **Decision:** Gantt refreshes on a 60s poll rather than subscribing to `TaskMoved` broadcast.
- **Consequences:** Cheaper than a live subscription for a schedule view; acceptable staleness ([[unknowns]]).

## ADR: JS lib bundled via vite theme, no CDN *(assumed)*

- **Decision:** `frappe-gantt` (or similar) is bundled in the panel theme; no external CDN.
- **Consequences:** CSP-friendly, offline-capable; larger theme bundle.
