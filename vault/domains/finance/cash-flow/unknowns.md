---
domain: finance
module: cash-flow
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cash Flow — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Low-cash threshold** — the alert threshold is a company setting. *(assumed)*
- **Scenario shift magnitude** — best/worst-case toggles shift inflows by ± 2 weeks. *(assumed)*

## UNVERIFIED gaps

- **Permission split** — the spec lists `finance.cashflow.view` and `finance.cashflow.manage-items`, but the canonical access-contract pattern uses `finance.cashflow.view-any`. Whether `view-any` exists as a distinct permission is unresolved.
- **Recurring expenses source** — outflows include "recurring expenses" but the spec does not name the source table/module (expenses? a recurring-bill construct in AP?).
- **Payroll estimate basis** — payroll outflow estimates are pulled from hr.payroll when present, but the estimation method (last run? scheduled run amount?) is not specified.
- **Bank opening basis** — opening cash comes from "bank balances"; whether this is the sum of all active bank accounts or a designated operating account is not specified.

No build-blocking unknowns identified.
