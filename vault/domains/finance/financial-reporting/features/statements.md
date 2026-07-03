---
domain: finance
module: financial-reporting
feature: statements
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Core Financial Statements

P&L, balance sheet, and cash flow statement, each with comparison columns, drill-down, and export.

- Three #9 report custom pages ([[../../../../architecture/ui-strategy]]): `ProfitLossPage`, `BalanceSheetPage`, `CashFlowStatementPage`, each with period selection (month / quarter / year / custom range) honouring fiscal-year config from Company Settings.
- **P&L**: revenue / COGS / expenses / net profit by period; comparison columns vs prior period and (when budgets active) vs budget.
- **Balance sheet**: assets / liabilities / equity as-of snapshot; asserts `assets = liabilities + equity` and alarms on imbalance.
- **Cash flow**: operating / investing / financing sections, indirect method *(assumed)*.
- **Drill-down**: clicking a statement line lists the contributing journal entries, which sum back to the line (brick/money, integer cents).
- **Export**: Excel + PDF per report (rate-limited); scheduled email delivery deferred to analytics.exports (P3).
- Closed periods served from 1 h cache; the current period is always computed live.

## UI
- **Kind**: custom-page (three report pages)
- **Page**: `ProfitLossPage` (`/finance/reports/pnl`), `BalanceSheetPage` (`/finance/reports/balance-sheet`), `CashFlowStatementPage` (`/finance/reports/cash-flow`).
- **Layout**: statement rows with comparison columns (prior period, and vs budget when active); period selector (month/quarter/year/custom) honouring fiscal-year config; drilldown to contributing journal entries; Excel + PDF export buttons.
- **Key interactions**: pick period, drill a line down to journal entries, export Excel/PDF (rate-limited).
- **States**: empty (no postings in period) · loading (statement skeleton) · error (imbalance alarm on balance sheet when assets ≠ liabilities + equity) · selected (drilldown expanded)
- **Gating**: `finance.reporting.view-any`

## Data
- Owns / writes: NO tables (`tables: []` — pure reporting). Any computed totals are integer minor units via brick/money.
- Reads (all read-only): finance.ledger (`fin_journal_*`, `fin_accounts`) for all figures; optionally finance.budgets (`fin_budget_lines`) for comparison columns. Cash flow uses the indirect method *(assumed)*. Closed periods cached 1 h; current period computed live.
- Cross-domain writes: none — reporting only. Never writes any domain's tables ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no events.
- Feeds: no events; scheduled email delivery deferred to analytics.exports (P3). In-domain / cross-domain read calls into ledger and budgets only.

## Test Checklist

### Unit
- [ ] P&L subtotals + net profit sum correctly from section rows (brick/money, integer cents); comparison deltas vs prior period / budget derived last
- [ ] Balance-sheet `balances` assertion true when `assets_cents == liabilities_cents + equity_cents`, false on an imbalanced fixture

### Feature (Pest)
- [ ] `profitLoss` / `balanceSheet` / `cashFlow` match GL fixture math; drill-down lines sum exactly back to their statement line
- [ ] Balance-sheet imbalance raises the data-corruption alarm (Sentry) rather than rendering silently corrupt figures
- [ ] Closed/historical period served from the 1 h cache; current period computed live; tenant isolation on the ledger read (company A cannot read company B journal lines)
- [ ] vs-budget comparison column present only when `finance.budgets` active; fiscal-year period boundaries honour Company Settings

### Livewire
- [ ] Each report page renders its statement + comparison columns and drills a line down to journal entries; `canAccess` denied without `finance.reporting.view-any`
- [ ] Excel / PDF export actions require `finance.reporting.export` and are governed by the `exports` rate limiter

See [[../architecture]], [[../api]], [[../data-model]], [[../security]].
