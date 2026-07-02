---
domain: projects
module: time-tracking
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Time Tracking — Unknowns

## Assumed Items

- Project-level entries (no task) allowed *(assumed)*.
- Approval is week-level approve-all *(assumed)*.
- One-running-timer enforced via partial index + service *(assumed)*.
- Finance integration is CSV-only in v1 *(assumed)*.

## Open Questions

- Automated billable-hours → invoice draft integration (event vs read API) — the later ADR.
- Billing rates: per-user, per-project, or per-task? Where do rates live (Finance vs here)?
- Passive/AI time capture (see [[../_opportunities]]) — future differentiator?
- Timer idle-detection / auto-stop on inactivity?
