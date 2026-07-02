---
domain: operations
module: operations-reporting
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Operations Reporting — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers)

- **Dead-stock window** — "no movement in N days" defaults to 90 *(assumed)*. Confirm default + whether it's a company setting.
- **Cache TTLs** — 1 h historical / 15 min current *(assumed)*; tune against real query cost.
- **Turnover formula** — turnover ratio = COGS / average inventory over the period *(assumed)*; confirm the exact definition (units vs value, period basis).
- **No stored tables** — the owns-no-tables decision is *(assumed)* right for v1 scale.

## Open Questions

- **Real-time / event-invalidated cache** — should the current-window metrics refresh on stock movements/receipts rather than TTL? Deferred *(assumed)*; relates to the real-time multi-location visibility gap in [[../../_opportunities]].
- **Forecasting** — demand forecasting / reorder suggestion is a competitor differentiator ([[../../_opportunities]]) but not in this module's v1 scope. Confirm whether it belongs here, in inventory, or in a dedicated future module.
