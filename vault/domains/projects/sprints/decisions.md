---
domain: projects
module: sprints
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Sprints — Decisions

## ADR: One active sprint per project

- **Decision:** Starting a sprint while another is active throws `ActiveSprintExistsException`.
- **Consequences:** Clear "current sprint"; sprint board always has one target.

## ADR: Burndown/velocity computed, no snapshot table *(assumed)*

- **Context:** Burndown needs per-day remaining points.
- **Decision:** Derive burndown from task completion timestamps at read time rather than storing daily snapshots.
- **Consequences:** No extra table; relies on accurate `completed_at`; historical burndown re-derivable. Revisit if performance suffers ([[unknowns]]).

## ADR: Complete-sprint moves incomplete tasks by user choice

- **Decision:** `CompleteSprint` takes `incomplete_action` (backlog / next-sprint); moves are atomic and record velocity.
- **Consequences:** No orphaned in-progress work; velocity captured at completion.
