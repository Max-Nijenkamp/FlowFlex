---
type: domain-index
domain: Finance & Accounting
panel: finance
color: "#4ADE80"
---

# Finance & Accounting

Complete accounting stack: general ledger, invoicing, expenses, AP/AR, bank reconciliation, budgets, financial reporting, and FP&A features. **Panel:** `/finance` (Emerald)

**Displaces**: Xero, QuickBooks, Sage, FreshBooks

---

## Navigation Groups

- **Ledger** — General Ledger, Fixed Assets
- **Invoicing** — Invoices, Accounts Receivable
- **Expenses** — Expenses, Accounts Payable
- **Planning** — Budgets, Forecasting, Cash Flow
- **Reporting** — Financial Reports, Tax Management

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/finance/invoicing\|Invoicing]] | `finance.invoicing` | planned | **MVP core** |
| [[domains/finance/expenses\|Expenses]] | `finance.expenses` | planned | **MVP core** |
| [[domains/finance/general-ledger\|General Ledger]] | `finance.ledger` | planned | MVP |
| [[domains/finance/bank-accounts\|Bank Accounts]] | `finance.bank` | planned | MVP |
| [[domains/finance/accounts-receivable\|Accounts Receivable]] | `finance.ar` | planned | Phase 2 |
| [[domains/finance/accounts-payable\|Accounts Payable]] | `finance.ap` | planned | Phase 2 |
| [[domains/finance/budgets\|Budgets]] | `finance.budgets` | planned | Phase 2 |
| [[domains/finance/financial-reporting\|Financial Reporting]] | `finance.reporting` | planned | Phase 2 |
| [[domains/finance/tax-management\|Tax Management]] | `finance.tax` | planned | Phase 2 |
| [[domains/finance/multi-currency\|Multi-Currency]] | `finance.currency` | planned | Phase 2 |
| [[domains/finance/forecasting\|Forecasting]] | `finance.forecasting` | planned | Phase 3 |
| [[domains/finance/cash-flow\|Cash Flow]] | `finance.cashflow` | planned | Phase 3 |
| [[domains/finance/fixed-assets\|Fixed Assets]] | `finance.assets` | planned | Phase 3 |

---

## Absorbed Domains

**FP&A** (formerly standalone) — budgeting and forecasting live in [[domains/finance/budgets]] and [[domains/finance/forecasting]].

---

## Key Patterns

- `spatie/laravel-model-states` — invoice status (draft → sent → paid → overdue), expense status
- `lorisleiva/laravel-actions` — simpler operations like `MarkInvoiceAsPaid`, `RecalculateInvoiceTotals`
- All amounts stored as integers (cents/minor currency units) — never floats
- Currency from [[domains/core/company-settings]] — no per-record currency unless Multi-Currency module active
