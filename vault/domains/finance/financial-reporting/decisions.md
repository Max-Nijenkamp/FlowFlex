---
domain: finance
module: financial-reporting
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Financial Reporting — Decisions

## No owned tables — pure reporting layer

The module deliberately owns no persistence. Every statement is generated at request time from the ledger, so figures always reflect the current posted state and there is no report table to keep in sync.

## Cash flow via the indirect method

The cash flow statement is intended to use the indirect method (net income adjusted for non-cash items and working-capital changes) *(assumed)*, the common SME default. Overridable via ADR.

## Section mapping by account type + code range

P&L / balance-sheet sections are derived from account `type` plus code ranges from the default chart of accounts *(assumed)* — e.g. the COGS/operating split follows a code convention rather than an explicit per-account flag.

## Current period never cached

Historical/closed periods cache for 1 h; the current period is served live because every posting changes it. This avoids serving stale in-progress figures.

## Balance assertion is an alarm, not a soft warning

A balance-sheet imbalance is treated as data corruption and raised to Sentry, not rendered as-is.

See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]], [[unknowns]].
