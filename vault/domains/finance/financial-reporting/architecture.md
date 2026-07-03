---
domain: finance
module: financial-reporting
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

Each report exports to Excel (`pxlrbt/filament-excel`) and PDF (`spatie/laravel-pdf`). Export actions carry the `exports` rate limiter to prevent export abuse / resource exhaustion. Scheduled email delivery is P3, deferred to analytics.exports.

## Filament Artifacts

**Nav group:** Reports *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ProfitLossPage` | #9 report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — revenue/COGS/expenses/net-profit rows, comparison columns (prior period, budget when active), drill-down to journal entries; realtime none | `/finance/reports/pnl`; Excel + PDF export actions carry the `exports` rate limiter |
| `BalanceSheetPage` | #9 report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — assets/liabilities/equity as-of snapshot, asserts `assets = liabilities + equity`; realtime none | `/finance/reports/balance-sheet`; Excel + PDF export actions carry the `exports` rate limiter |
| `CashFlowStatementPage` | #9 report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — operating/investing/financing sections (indirect method *(assumed)*); realtime none | `/finance/reports/cash-flow`; Excel + PDF export actions carry the `exports` rate limiter |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.reporting.view-any') && BillingService::hasModule('finance.reporting')`
per [[../../../architecture/filament-patterns]] #1. `ProfitLossPage`, `BalanceSheetPage`, and `CashFlowStatementPage` are custom pages and MUST state this explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Statement rendering (P&L / balance sheet / cash flow) | n-a | read-only derived computation over ledger `fin_journal_*` + `fin_accounts`; the module owns no tables and writes nothing |
| Excel / PDF export | n-a | read-only file generation from the computed statement — no persisted writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/custom-pages]], [[../../../architecture/packages]], [[data-model]], [[api]].
