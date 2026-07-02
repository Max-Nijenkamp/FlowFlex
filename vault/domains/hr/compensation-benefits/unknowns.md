---
domain: hr
module: compensation-benefits
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Unknowns — Compensation & Benefits

## Assumptions carried from spec

- *(assumed)* Payroll reads active benefit enrollments at run time (benefit cost reflected in the next payroll run rather than pushed on enrollment).

## Open questions

- None recorded in the source spec beyond the assumption above.

## Unverified

- Whole module is `build-status: planned` — no code exists after the strip ([[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]). All behavior, tables, DTOs, services, and permissions here are intended targets, not verified against any implementation or passing tests.
- The `salary_band` derivation rule (how a cents amount maps to a coarse band label) is not specified.

## Related

- [[_module]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
