---
domain: finance
module: budgets
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Budgets — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Variance-alert threshold** — defaults to 10%, surfaced as a company setting. *(assumed)*
- **Approved-line immutability** — approved budget lines are locked; changes require a revision. *(assumed)*

## UNVERIFIED gaps

- **`remaining()` consumption source** — `remaining()` subtracts "consumed to date", but the spec does not name whether consumption is committed POs (procurement), posted actuals (ledger), or both.
- **Scope resolution** — `scope_id` references a department or project, but the referenced tables (`hr` departments? a projects module?) are not named.
- **Rolling forecast view** — the "rolling forecast view (links Forecasting)" surface is listed but its exact placement (a tab on the budget, a forecasting page) is unspecified.

No build-blocking unknowns identified.
