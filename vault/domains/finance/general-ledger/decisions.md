---
domain: finance
module: general-ledger
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# General Ledger — Decisions

## Manual chart-of-accounts editing (founder override, 2026-06-12 sync)

GL accounts are intended to be manually creatable/editable in `/finance` (code, name, type, parent, active). The original spec said chart-on-demand only via `LedgerService`. Both paths are intended to coexist — `LedgerService` on-demand creation is unchanged.

## Posted-entry immutability

Posted journal entries are never edited or deleted; corrections flow through `reverse`. This is a hard accounting-integrity rule, not a preference.

## Money precision

Amounts are integer minor units via `brick/money`, never floats — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] context and the currency-precision decision referenced from [[../_index]].

See [[unknowns]].
