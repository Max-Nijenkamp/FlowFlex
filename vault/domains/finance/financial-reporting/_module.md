---
domain: finance
module: financial-reporting
type: module
module-key: finance.reporting
priority: v1
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac, core.settings]
soft-depends: [finance.budgets, analytics.exports]
fires-events: []
consumes-events: []
patterns: [custom-pages, money, pdf]
tables: []
permission-prefix: finance.reporting
encrypted-fields: []
color: "#4ADE80"
updated: 2026-06-20
---

# Financial Reporting

P&L statement, balance sheet, and cash flow statement generated from the General Ledger — the core financial statements every business needs. Owns **no tables**: pure reporting over the ledger. This is the canonical reporting note for finance.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Purpose

The module renders the three core financial statements over any period — Profit & Loss, Balance Sheet, and Cash Flow Statement — driven entirely by the general ledger. It supports comparative periods (this vs prior vs budget), drill-down from a statement line to contributing journal entries, fiscal-year-aware period selection, and export to Excel/PDF. It owns no source data of its own.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | the only data source |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/company-settings/_module\|core.settings]] | gating, permissions, fiscal-year config |
| Soft | [[../budgets/_module\|finance.budgets]] | vs-budget comparison column; hidden without it |
| Soft | [[../../analytics/scheduled-exports/_module\|analytics.exports]] | scheduled email delivery (P3); manual export until then |

## Core Features

- Profit & Loss (income statement): revenue, COGS, expenses, net profit by period.
- Balance sheet: assets, liabilities, equity snapshot — must balance (assets = liabilities + equity assertion).
- Cash flow statement: operating, investing, financing activities (indirect method *(assumed)*).
- Comparative periods: this period vs prior period vs budget.
- Drill-down: click a line to see contributing journal entries.
- Period selection: month, quarter, year, custom range.
- Export to Excel and PDF (`pxlrbt/filament-excel`, `spatie/laravel-pdf`).
- Fiscal-year configuration from Company Settings.

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:finance:pl:{period}` etc. | 1 h, **closed/historical periods only** | posting into period; current period never cached ([[../../../architecture/caching]]) |

## Permissions

`finance.reporting.view` · `finance.reporting.export`

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] P&L net profit matches GL fixture math (brick/money)
- [ ] Balance sheet balances; imbalance raises alert
- [ ] Comparison columns correct vs prior period + budget
- [ ] Drill-down lines sum to report line
- [ ] Historical period served from cache; current period live
- [ ] Fiscal-year period boundaries respect settings

## Build Manifest

```
app/Data/Finance/{ProfitLossData,BalanceSheetData,CashFlowStatementData}.php
app/Contracts/Finance/ReportingServiceInterface.php
app/Services/Finance/ReportingService.php
app/Filament/Finance/Pages/{ProfitLossPage,BalanceSheetPage,CashFlowStatementPage}.php
tests/Feature/Finance/{ProfitLossTest,BalanceSheetTest,CashFlowTest}.php
```

## Cross-Domain Edges

**Data ownership.** This module owns no tables — it is pure reporting and writes nothing; all data is read-only from other finance modules, never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Reads | `fin_journal_*` + `fin_accounts` (read-only) | [[../general-ledger/_module\|finance.ledger]] |
| Reads | `fin_budget_lines` (read-only) | [[../budgets/_module\|finance.budgets]] |

## Entity Notes

- [[architecture]] — statement assembly, balance assertion, caching, export
- [[data-model]] — no owned tables; source mapping
- [[api]] — output DTOs, service methods, events
- [[security]] — access contract, permissions, export rate limit
- [[decisions]] — indirect cash flow, code-range mapping, current-period no-cache
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/statements]]

## Related

- [[../general-ledger/_module]]
- [[../budgets/_module]]
- [[../cash-flow/_module]]
- [[../../analytics/scheduled-exports/_module]]
- [[../../../architecture/caching]]
- [[../../../glossary]]
