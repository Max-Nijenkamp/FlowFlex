---
domain: finance
module: financial-reporting
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Financial Reporting — DTOs, Services & Events

## DTOs (output only)

The module produces reports, not writes; all DTOs are output shapes (`spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]]).

### ProfitLossData
Section rows (`label`, contributing account refs, `amount_cents`) + subtotals + net profit + comparison columns (prior period, budget).

### BalanceSheetData
Asset / liability / equity sections + totals, plus a `balances: bool` assertion result (`assets_cents == liabilities_cents + equity_cents`).

### CashFlowStatementData
Operating / investing / financing sections + net change in cash (indirect method *(assumed)*).

## Services & Actions

`ReportingServiceInterface` → `ReportingService`:

- `profitLoss(CarbonImmutable $from, CarbonImmutable $to, bool $compare = true): ProfitLossData`
- `balanceSheet(CarbonImmutable $asOf): BalanceSheetData` — asserts balance; imbalance = data-corruption alarm (Sentry).
- `cashFlow(CarbonImmutable $from, CarbonImmutable $to): CashFlowStatementData`
- Section mappings driven by account `type` + code ranges *(assumed: COGS/operating split via code convention from the default CoA)*.

All amounts are integer minor units (cents) via brick/money.

## Events

This module fires and consumes no cross-domain events. It reads the ledger (and, when active, budget lines) directly within the finance domain. Scheduled delivery integrates with analytics.exports at P3, not via the event bus.

See [[security]], [[features/statements]], [[../general-ledger/_module]].
