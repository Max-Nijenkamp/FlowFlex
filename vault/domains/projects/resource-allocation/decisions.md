---
domain: projects
module: resource-allocation
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Resource Allocation — Decisions

## ADR: Over-allocation warns, does not block *(assumed)*

- **Context:** Planners sometimes intentionally over-commit a person short-term.
- **Decision:** Summing >100% across overlapping allocations raises a warning flag, not a rejection.
- **Consequences:** Flexible planning; over-allocation surfaced as a badge/alert, not an error.

## ADR: Actual utilisation reads time entries, degrades gracefully

- **Decision:** `utilisation()` compares planned % to logged time when `projects.time` is active; omits actual otherwise.
- **Consequences:** Allocation works standalone; the plan-vs-actual view lights up once time tracking is on.

## ADR: Allocation is a percentage, not hours *(assumed)*

- **Decision:** Model allocation as % of a person's time over a date range, independent of task estimates.
- **Consequences:** Coarser than task-hour workload (that's projects.workload's job); the two overlay.
