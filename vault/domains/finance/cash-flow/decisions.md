---
domain: finance
module: cash-flow
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cash Flow — Decisions

## Full-regenerate rebuild strategy

The nightly rebuild is intended to **delete and rebuild all projected rows** rather than incrementally patch them. This keeps the projection deterministic — re-running produces identical output — at the cost of recomputing the full 13-week horizon each night. Actual (`is_actual = true`) rows are backfilled separately and not affected.

## Scenario shift affects inflows only

Best/worst-case scenario toggles shift **inflow timing** by ± 2 weeks and leave outflows fixed *(assumed)*. Rationale: collection timing is the uncertain variable; scheduled bills/payroll dates are comparatively firm. Overridable via ADR.

## 13-week horizon

The horizon is fixed at 13 weeks — the standard treasury planning window — rather than configurable per company.

See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]], [[unknowns]].
