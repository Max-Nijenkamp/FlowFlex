---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.reporting
status: planned
color: "#4ADE80"
---

# Financial Reporting

> Profit & Loss, balance sheet, and cash flow statement — read-only statutory report views generated from the General Ledger for any period.

**Panel:** `finance`
**Module key:** `finance.reporting`

## What It Does

Financial Reporting generates the three core statutory financial statements — Profit & Loss (income statement), Balance Sheet, and Cash Flow Statement — directly from the General Ledger data. These are read-only custom Filament pages. Finance selects a period (month, quarter, or financial year) and the reports compute in real time from GL journal entries. No data is entered here — all financial data flows from invoicing, expenses, payroll, and manual journals posted to the GL. Reports can be compared across two periods and exported for accountant review or board presentation.

## Features

### Core
- Profit & Loss report: Revenue − Cost of Goods − Operating Expenses = Net Profit — grouped by account type, line items per GL account
- Balance Sheet: Assets = Liabilities + Equity — as at a specific date; computed from all GL postings up to that date
- Cash Flow Statement: indirect method — Net Profit + adjustments for non-cash items and working capital changes
- Period selector: month, quarter, custom date range, or financial year
- Export: download any report as PDF or Excel

### Advanced
- Comparative reporting: show two periods side by side (e.g. this quarter vs last quarter, this year vs last year) with variance column
- Department P&L: filter P&L to a specific department — requires expense GL entries to include a department dimension
- Budget vs actual overlay: P&L with budgeted amounts alongside actuals — shows variance per line (requires Budgets module)
- Consolidated reporting: if multiple entities are configured, produce a consolidated group P&L with inter-company elimination
- YTD and full-year projection: P&L shows YTD actuals + projected remaining months (current run rate × remaining months)

### AI-Powered
- Narrative commentary: AI generates a one-paragraph executive summary of the P&L highlighting the three most significant drivers of change vs the prior period
- Anomaly callouts: unusual line items (revenue spike, expense category that doubled) highlighted with an asterisk and AI explanation in the report

## Data Model

```erDiagram
    financial_report_cache {
        ulid id PK
        ulid company_id FK
        string report_type
        string period
        json data
        timestamp generated_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `report_type` | profit_loss / balance_sheet / cash_flow |
| `period` | e.g. `2026-Q1`, `2026-01`, `FY2026` |
| `data` | Pre-computed report JSON for fast render; invalidated when new journals post |

## Permissions

- `finance.reporting.view-pl`
- `finance.reporting.view-balance-sheet`
- `finance.reporting.view-cashflow`
- `finance.reporting.export`
- `finance.reporting.view-department`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `ProfitLossPage`, `BalanceSheetPage`, `CashFlowStatementPage`
- **Widgets:** `NetProfitWidget`, `RevenueVsExpenseWidget` — KPI cards on finance dashboard
- **Nav group:** Reporting (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Reports | Financial reporting and statements |
| QuickBooks Reports | P&L and balance sheet |
| Sage Reporting | Financial report generation |
| FreshBooks | Basic profit and loss reporting |

## Implementation Notes

**Filament:** `ProfitLossPage`, `BalanceSheetPage`, and `CashFlowStatementPage` are all custom `Page` classes. None use standard Filament tables — each renders a hierarchical account tree with indented rows, subtotals, and a grand total. The report data is computed by a report service (`app/Services/Finance/FinancialReportService.php`) that aggregates `journal_lines` grouped by GL account, filtered by the selected period. The service returns a structured PHP array that the Blade view renders as an HTML table.

**Report caching:** `financial_report_cache` stores pre-computed report JSON keyed by `(company_id, report_type, period)`. The cache entry is invalidated (deleted) whenever a new journal entry is posted for that period — handle this in the `JournalPosted` event listener. On page load, check cache first; if miss, compute and store. Cache TTL: indefinite until invalidated.

**PDF/Excel export:** The spec requires export to PDF and Excel. These are not in the current tech stack explicitly:
- **PDF:** `barryvdh/laravel-dompdf` renders the report Blade view to PDF. The report Blade view needs a print stylesheet. Add `dompdf` to `composer.json`.
- **Excel:** `maatwebsite/laravel-excel` (PhpSpreadsheet wrapper) generates `.xlsx` files. Add to `composer.json`. The report array maps cleanly to a spreadsheet with the `FromArray` concern.

**Cash flow statement (indirect method):** This is the most complex computation. Requires: (1) Net Profit from the P&L, (2) adding back non-cash items (depreciation — from fixed-assets module), (3) working capital changes (AR change, AP change, inventory change). All of these require the GL to be organised with correct account types. The service must know which GL accounts represent AR, AP, inventory, and depreciation — this requires a chart of accounts setup with account type classifications (Asset, Liability, Equity, Revenue, Expense) already built in the `general-ledger` module.

**Consolidated reporting:** If multiple entities are configured, the consolidation engine must eliminate intercompany transactions (GL entries where the counterparty `company_id` is a sibling entity). This requires an intercompany transaction flag on journal entries — add `boolean is_intercompany` and `ulid counterparty_company_id` to `journal_lines` now even if consolidation is Phase 3.

**AI features:** Narrative commentary and anomaly callouts both call `app/Services/AI/FinancialReportNarrativeService.php` with a summarised version of the P&L (account names, current values, prior period values, variances). OpenAI GPT-4o returns a paragraph and a list of flagged line items.

## Related

- [[general-ledger]]
- [[budgets]]
- [[invoicing]]
- [[expenses]]
- [[tax-management]]
