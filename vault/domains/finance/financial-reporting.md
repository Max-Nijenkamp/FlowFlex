---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.reporting
status: planned
priority: v1
depends-on: [finance.ledger, core.billing, core.rbac, core.settings]
soft-depends: [finance.budgets, analytics.exports]
fires-events: []
consumes-events: []
patterns: [custom-pages, money, pdf]
tables: []
permission-prefix: finance.reporting
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Financial Reporting

P&L statement, balance sheet, and cash flow statement generated from the General Ledger. The core financial statements every business needs. Owns no tables — pure reporting over the ledger.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | the only data source |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/company-settings\|core.settings]] | gating, permissions, fiscal year config |
| Soft | [[domains/finance/budgets\|finance.budgets]] | vs-budget comparison column; hidden without it |
| Soft | [[domains/analytics/scheduled-exports\|analytics.scheduled-exports]] | scheduled email delivery (P3); manual export until then |

---

## Core Features

- Profit & Loss (income statement): revenue, COGS, expenses, net profit by period
- Balance sheet: assets, liabilities, equity snapshot — must balance (assets = liabilities + equity assertion)
- Cash flow statement: operating, investing, financing activities (indirect method *(assumed)*)
- Comparative periods: this period vs prior period vs budget
- Drill-down: click a line to see contributing journal entries
- Period selection: month, quarter, year, custom range
- Export to Excel and PDF (`pxlrbt/filament-excel`, `spatie/laravel-pdf`)
- Fiscal year configuration from Company Settings

---

## Data Model

No additional tables. Generated from `fin_accounts`, `fin_journal_entries`, `fin_journal_lines`.

## DTOs

Output only: `ProfitLossData`, `BalanceSheetData`, `CashFlowStatementData` — section rows (label, account refs, amount_cents) + totals + comparison columns.

## Services & Actions

Interface→Service: `ReportingServiceInterface` → `ReportingService`.

- `profitLoss(CarbonImmutable $from, CarbonImmutable $to, bool $compare = true): ProfitLossData`
- `balanceSheet(CarbonImmutable $asOf): BalanceSheetData` — asserts balance; imbalance = data corruption alarm (Sentry)
- `cashFlow(CarbonImmutable $from, CarbonImmutable $to): CashFlowStatementData`
- All section mappings driven by account `type` + code ranges *(assumed: COGS/operating split via code convention from default CoA)*

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:finance:pl:{period}` etc. | 1 h, **closed/historical periods only** | posting into period; current period never cached ([[architecture/caching]]) |

---

## Filament

**Nav group:** Reporting

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ProfitLossPage` | #9 report custom page | comparison columns, drill-down |
| `BalanceSheetPage` | #9 report custom page | |
| `CashFlowStatementPage` | #9 report custom page | |

Export + (P3) schedule actions per report.

---

## Permissions

`finance.reporting.view` · `finance.reporting.export`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] P&L net profit matches GL fixture math (brick/money)
- [ ] Balance sheet balances; imbalance raises alert
- [ ] Comparison columns correct vs prior period + budget
- [ ] Drill-down lines sum to report line
- [ ] Historical period served from cache; current period live
- [ ] Fiscal-year period boundaries respect settings

---

## Build Manifest

```
app/Data/Finance/{ProfitLossData,BalanceSheetData,CashFlowStatementData}.php
app/Contracts/Finance/ReportingServiceInterface.php
app/Services/Finance/ReportingService.php
app/Filament/Finance/Pages/{ProfitLossPage,BalanceSheetPage,CashFlowStatementPage}.php
tests/Feature/Finance/{ProfitLossTest,BalanceSheetTest,CashFlowTest}.php
```

---

## Related

- [[domains/finance/general-ledger]]
- [[domains/finance/budgets]]
- [[domains/analytics/scheduled-exports]]
- [[architecture/caching]]
