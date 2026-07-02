---
domain: finance
module: financial-reporting
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Financial Reporting — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Cash flow method** — the cash flow statement uses the indirect method. *(assumed)*
- **Section mapping** — statement sections derive from account `type` + code ranges (COGS/operating split via code convention from the default CoA). *(assumed)*

## UNVERIFIED gaps

- **Permission split** — the spec lists `finance.reporting.view` / `finance.reporting.export`, but the canonical access-contract pattern uses `finance.reporting.view-any`. Whether `view-any` exists as a distinct permission is unresolved.
- **Custom-range fiscal boundaries** — period selection supports "custom range", but how a custom range interacts with fiscal-year period boundaries (and comparison-column alignment) is not fully specified.
- **Comparison budget selection** — the vs-budget column reads from budgets, but which budget version/scope is chosen when several exist for the fiscal year is not specified.

No build-blocking unknowns identified.
