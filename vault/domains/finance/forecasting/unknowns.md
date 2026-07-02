---
domain: finance
module: forecasting
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Accuracy metric** — forecast accuracy uses a MAPE-style metric over closed periods. *(assumed)*

## UNVERIFIED gaps

- **Growth application granularity** — `seedFromActuals` applies a single `growthPercent`; whether growth can vary per account or per period is not specified.
- **Driver model definition** — driver-based forecasting is listed, but the schema for storing driver definitions and the formula linking drivers to revenue lines is not specified (no driver table in the data model).
- **Trailing window** — seed-from-actuals uses "trailing 12 months"; whether this window is configurable is not stated.
- **Permission split** — the spec lists `view-any` / `create` / `update`; whether a `view` (non-any) permission is also needed for scoped viewers is unresolved.

No build-blocking unknowns identified.
