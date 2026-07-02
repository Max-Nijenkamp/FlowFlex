---
domain: finance
module: tax-management
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tax Management — Decisions

## Basis-points rate storage (new vs v1 spec)

Rates are stored as integer **basis points** (`rate_basis_points`, e.g. `2100` = 21%) rather than the original `rate_percent` float column. This keeps all tax math integer-only and consistent with the brick/money minor-unit rule — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] context and the currency-precision decision referenced from [[../_index]].

## Report-only OSS / VAT return (no filing integration v1)

OSS reporting and the VAT return are intended as a summary/preparation report only — there is no OSS filing integration in v1 *(assumed)*. This is overridable via ADR.

## VIES failure-tolerant

VAT number validation via VIES is intended to be failure-tolerant: a network failure marks the number "unverified" and never blocks a save *(assumed)*.

See [[unknowns]].
