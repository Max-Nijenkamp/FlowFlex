---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.reporting
status: planned
color: "#4ADE80"
---

# Financial Reporting

P&L statement, balance sheet, and cash flow statement generated from the General Ledger. The core financial statements every business needs.

## Core Features

- Profit & Loss (income statement): revenue, COGS, expenses, net profit by period
- Balance sheet: assets, liabilities, equity snapshot
- Cash flow statement: operating, investing, financing activities
- Comparative periods: this period vs prior period vs budget
- Drill-down: click a line to see contributing journal entries
- Period selection: month, quarter, year, custom range
- Export to Excel and PDF (`maatwebsite/laravel-excel`, `spatie/laravel-pdf`)
- Scheduled email delivery (links Analytics scheduled exports)
- Fiscal year configuration from Company Settings

## Data Model

No additional tables. Generated from `fin_accounts`, `fin_journal_entries`, `fin_journal_lines`. Heavy reports cached (see [[architecture/caching]]).

## Filament

**Nav group:** Reporting

- `ProfitLossPage` (custom page) — structured P&L with comparison
- `BalanceSheetPage` (custom page) — asset/liability/equity layout
- `CashFlowStatementPage` (custom page) — cash flow statement
- Export + schedule actions per report

## Cross-Domain / Performance

- Reads exclusively from General Ledger
- Historical-period reports cached 1hr (see [[architecture/caching]])

## Related

- [[domains/finance/general-ledger]]
- [[domains/analytics/scheduled-exports]]
- [[architecture/caching]]
