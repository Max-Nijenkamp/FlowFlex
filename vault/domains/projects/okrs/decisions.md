---
domain: projects
module: okrs
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# OKRs — Decisions

## ADR: Baseline-aware KR progress

- **Context:** A KR may start above 0 (e.g. improve NPS from 40 to 60).
- **Decision:** Progress = `(current − baseline) / (target − baseline)` clamped 0–100.
- **Consequences:** Accurate progress for non-zero starts; requires a `baseline_value` column.

## ADR: Objective progress cascades up the hierarchy

- **Decision:** Objective progress = average of its KRs; parent objectives average their children; cached in `progress_percent`.
- **Consequences:** Roll-up dashboards are cheap reads; cache recomputed on check-in.

## ADR: Hierarchy cycle + depth-4 guard *(assumed depth)*

- **Decision:** Reparenting is cycle-checked; max depth 4 (company → dept → team → individual) *(assumed)*.
- **Consequences:** Bounded tree; avoids pathological nesting ([[unknowns]]).

## ADR: Check-in reminders via weekly command

- **Decision:** `OkrCheckinReminderCommand` (weekly) nudges owners of KRs stale >7 days.
- **Consequences:** Keeps OKRs alive; window-safe idempotent.
