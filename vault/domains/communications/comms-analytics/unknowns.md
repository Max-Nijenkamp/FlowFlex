---
domain: communications
module: comms-analytics
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Comms Analytics — Unknowns

## Assumed Items

- Cache TTLs 1 h / 15 min *(from source)*.
- Heat-map bucketed by company timezone *(assumed detail)*.

## Open Questions

> [!warning] UNVERIFIED
> Whether aggregate queries over `comms_messages` stay performant at scale (large message volumes) without a pre-aggregated rollup table is unproven. If not, a nightly rollup (which this module would then own) may be needed — changing the "owns no tables" stance.

- SLA / business-hours-aware response time (exclude nights/weekends)?
- Resolution definition when a conversation is reopened multiple times.
- Export (CSV/PDF) of the dashboard — in scope?
- Per-team (not just per-agent) breakdown.

## Related

- [[_module]] · [[decisions]]
