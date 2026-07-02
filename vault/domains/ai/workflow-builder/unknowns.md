---
domain: ai
module: workflow-builder
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workflow Builder — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR.

---

## Open Questions

1. **v1 builder UI: list vs. visual canvas.** Specced as a #9 node editor; v1 may ship as a list-based builder with the drag-canvas deferred. Which ships first?
2. **Loop-guard mechanism.** Assumed a system-actor flag on workflow-produced events, guarding at depth 1. Confirm the actor-tagging approach against [[../../../architecture/event-bus]] — does every event carry an actor field?
3. **Scheduled-workflow cadence.** `RunScheduledWorkflowsCommand` assumed every 15 min with a next-run cursor. Confirm the granularity companies need (per-minute? cron expressions?).
4. **Run retention.** `ai_workflow_runs` pruned at 90 days *(assumed)*. Confirm against [[../../../architecture/data-lifecycle]].
5. **Webhook SSRF hardening.** Outbound webhook actions need target validation (block internal/loopback). What is the allow/deny policy?
6. **Route slugs.** `/app/ai/workflows/builder` and `/app/ai/workflows/runs` are assumed — confirm the `ai` panel slug.
7. **Action set for v1.** create task / send notification / call webhook / wait-delay / update record are assumed as the starting typed actions. Confirm the shipping set.

---

## Assumed Items (unverified)

- `*(assumed)*` — v1 may be a list-based builder; visual canvas later.
- `*(assumed)*` — loop guard via system-actor flag, depth 1.
- `*(assumed)*` — scheduled workflows evaluated every 15 min.
- `*(assumed)*` — `ai_workflow_runs` pruned at 90 days.
- `*(assumed)*` — routes `/app/ai/workflows/builder` + `/app/ai/workflows/runs`.
- `*(assumed)*` — v1 action set: create task, notify, webhook, wait/delay, update record.

> [!warning] UNVERIFIED
> The highest-impact unknowns are the **loop-guard mechanism** (a wrong assumption risks infinite cascades in production) and the **v1 builder UI** (list vs. canvas — a large front-end cost difference). Resolve both before build.
