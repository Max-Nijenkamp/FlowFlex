---
domain: projects
module: workload
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Workload — Decisions

## ADR: Pure view — owns no tables

- **Decision:** Workload is a projection of tasks + capacity; it owns no tables and mutates via `UpdateTaskAction`.
- **Consequences:** Data-ownership honoured; grid always reflects task truth.

## ADR: Capacity from HR when active, else 8h/day default

- **Decision:** Per-user daily capacity comes from the HR profile when `hr.profiles` is active; otherwise a flat 8h/day.
- **Consequences:** Works standalone; sharpens once HR is enabled. No write into `hr_*`.

## ADR: Due-date bucketing in v1 *(assumed)*

- **Context:** True workload spreads a task's hours across its start→due span.
- **Decision:** v1 buckets a task's full estimate on its due date; even spreading is a later refinement.
- **Consequences:** Simpler aggregate query; potential lumpiness noted ([[unknowns]]).

## ADR: Single aggregate query (no N+1)

- **Decision:** The grid is built in one aggregate query.
- **Consequences:** Scales to large teams; enforced by an N+1 test.
