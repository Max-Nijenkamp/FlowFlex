---
domain: projects
module: milestones
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Milestones — Decisions

## ADR: Plain enum status, no spatie state machine *(assumed)*

- **Context:** Milestone transitions are trivial (open → achieved | missed).
- **Decision:** Use a plain string enum rather than spatie/laravel-model-states.
- **Consequences:** Less ceremony; `missed` set by the scheduled command, `achieved` by the action.

## ADR: Progress via same-domain call, not an event

- **Decision:** `CompleteTaskAction` (projects.tasks) calls `MilestoneProgress::for()` directly — same Projects bounded context.
- **Consequences:** Synchronous, simple; only valid within-domain.

## ADR: 7-day reminder guarded by `reminded_at`

- **Decision:** The daily command sends a single 7-day-out reminder, guarded so re-runs don't duplicate.
- **Consequences:** Idempotent scheduling; one nudge per milestone.
