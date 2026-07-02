---
domain: finance
module: financial-reporting
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Financial Reporting — Architecture

`ReportingServiceInterface` → `ReportingService` (Interface→Service per [[../../../architecture/patterns/interface-service]]) assembles all three statements from the ledger. The module owns no tables — it is a pure read layer over `fin_accounts`, `fin_journal_entries`, `fin_journal_lines`.

## Money handling

All amounts are integer **minor units** (cents), summed and subtotalled with `brick/money` — never raw float math. Net profit, subtotals, and comparison deltas are integer-cent arithmetic. See [[../../../architecture/packages]] (brick/money).

## Statement assembly

- `profitLoss(from, to, compare)` — revenue / COGS / expenses / net profit by period; comparison columns for prior period and budget when available.
- `balanceSheet(asOf)` — assets / liabilities / equity snapshot. **Asserts** assets = liabilities + equity; an imbalance signals data corruption and raises a Sentry alarm (see [[../../../architecture/error-handling]]).
- `cashFlow(from, to)` — operating / investing / financing sections via the indirect method *(assumed)*.
- Section mappings are driven by account `type` + code ranges *(assumed: COGS/operating split via code convention from the default chart of accounts)*.

## Caching & current period

Closed/historical periods cache for 1 h under `company:{id}:finance:pl:{period}` (and balance-sheet / cash-flow equivalents), busted by posting into that period. The **current period is never cached** — it changes with every posting. See [[../../../architecture/caching]].

## Export

Each report exports to Excel (`pxlrbt/filament-excel`) and PDF (`spatie/laravel-pdf`). Export actions carry a rate limiter to prevent export abuse / resource exhaustion. Scheduled email delivery is P3, deferred to analytics.exports.

See [[../../../architecture/patterns/custom-pages]], [[../../../architecture/packages]], [[data-model]], [[api]].
