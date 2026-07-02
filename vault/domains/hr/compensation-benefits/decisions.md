---
domain: hr
module: compensation-benefits
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Decisions — Compensation & Benefits

## Salary history is append-only

`hr_salary_history` is an append-only audit trail: each salary change writes a new row (who changed, when, old → new, reason). No update or delete is permitted — enforced at the model/arch-test level. See [[../../../decisions/decision-2026-06-01-salary-history]].

Consequence: `adjustSalary` must write the history row and the payroll profile update inside a single transaction (see [[architecture]]).

## Related

- [[../../../decisions/decision-2026-06-01-salary-history]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
