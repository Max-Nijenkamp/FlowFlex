---
domain: hr
module: payroll
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payroll — Unknowns & Assumptions

Every `*(assumed)*` marker and open question from the source spec. Each is an authoritative default at build time, overridable by ADR.

## Assumptions

- `hr.compensation` benefit deductions feed payslips — soft integration *(assumed)*.
- `hr_payroll_employees.hourly_rate_raw` is an encrypted integer-cents column *(assumed)*.
- `hr_deduction_types.value` uses basis points for percent deductions *(assumed)* (cents for flat).
- Approver ≠ run creator — four-eyes control on payroll approval *(assumed)*.
- IBAN validation uses a custom rule, not propaganistas/laravel-phone *(noted — propaganistas not applicable)*.

## Open Questions

- **Tax calculation depth:** v1 = configurable flat/percent deduction types only, NO statutory tax engine *(assumed)*. Real NL/DE tax tables are a Phase 2+ integration decision (ADR when needed).

## Unverified (rebuild context)

- Whole module is `build-status: planned` — code stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. No implementation, migrations, jobs, or tests exist yet; all behavior below is intended design.

## Related
- [[_module]] · [[architecture]] · [[security]]
