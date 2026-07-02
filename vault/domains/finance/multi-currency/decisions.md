---
domain: finance
module: multi-currency
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Multi-Currency — Decisions

## brick/money minor-unit precision

Per-currency precision is driven by ISO 4217 `minor_unit_digits` (0–3 — JPY=0, BHD=3, most=2), and all amounts are integer minor units handled by `brick/money`, never floats. This is the canonical currency-precision rule — see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] context and the currency-precision decision referenced from [[../_index]].

## GL always base currency

The General Ledger always posts base currency and never stores foreign-currency amounts. Conversion (rate locked at transaction date) happens before posting; FX differences land in dedicated FX gain/loss accounts. This is a hard accounting-integrity rule, not a preference.

## Manual rates v1, API hook later

Exchange rates are intended to be entered manually in v1, with an API feed hook deferred *(assumed)*. Historical rates are kept (effective-dated rows). Overridable via ADR.

See [[unknowns]].
