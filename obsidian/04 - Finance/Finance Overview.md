---
tags: [flowflex, domain/finance, overview, phase/3]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: built
last_updated: 2026-05-07
---

# Finance Overview

The financial nerve system of the business. From invoices to payroll costs, from bank reconciliation to VAT returns. Every number in the platform that involves money routes through here.

**Filament Panel:** `finance`
**Domain Colour:** Emerald `#059669` / Light: `#D1FAE5`
**Domain Icon:** `banknotes` (Heroicons)
**Phase:** 3 (core: Invoicing, Expenses, Financial Reporting) · 8 (extensions: AP/AR, Bank Reconciliation, Budgeting, Client Billing, Tax/VAT, Fixed Assets, MRR Tracking)

## Modules in This Domain

| Module | Phase | Description |
|---|---|---|
| [[Invoicing]] | 3 | Invoice builder, auto-generate from time/milestones |
| [[Expense Management]] | 3 | Mobile receipt, OCR, approval, payroll reimbursement |
| [[Financial Reporting]] | 3 | P&L, balance sheet, cash flow, custom reports |
| [[Accounts Payable & Receivable]] | 8 | Supplier bills, payment runs, aged reports |
| [[Bank Reconciliation]] | 8 | Open banking, auto-match, manual reconciliation |
| [[Budgeting & Forecasting]] | 8 | Department budgets, actuals, variance, scenarios |
| [[Client Billing & Retainers]] | 8 | Time-to-invoice, retainer drawdown, milestone billing |
| [[Tax & VAT Compliance]] | 8 | VAT/GST, MTD, multi-jurisdiction, filing status |
| [[Fixed Asset & Depreciation]] | 8 | Asset register, depreciation schedules (SL/DB), disposal |
| [[Subscription & MRR Tracking]] | 8 | MRR/ARR, churn, expansion revenue, recognition |
| [[Multi-Currency & FX Management]] | 6 | ECB rates, FX revaluation, journal entries |
| [[Open Banking & Bank Feeds]] | 6 | GoCardless/Plaid, AI categorisation, 2,300+ EU banks |
| [[Cash Flow Forecasting & Scenario Planning]] | 6 | 13-week rolling forecast, Float replacement |
| [[Revenue Recognition]] | 6 | IFRS 15 / ASC 606 automation, deferred revenue, journal entries |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `InvoiceOverdue` | [[Invoicing]] | CRM (follow-up task), [[Notifications & Alerts]] |
| `InvoicePaid` | [[Invoicing]] | [[Bank Reconciliation]] (auto-match), [[Subscription & MRR Tracking]] |
| `ExpenseApproved` | [[Expense Management]] | [[Payroll]] (add reimbursement to pay run) |
| `BillPaid` | [[Accounts Payable & Receivable]] | — |
| `PaymentRunCompleted` | [[Accounts Payable & Receivable]] | — |

## Key Events Consumed from Other Domains

| Event | From | What Finance Does |
|---|---|---|
| `TimeEntryApproved` | [[Time Tracking]] | Marks time as billable in [[Client Billing & Retainers]] |
| `ProjectMilestoneReached` | [[Project Planning]] | Triggers milestone invoice in [[Invoicing]] |
| `FieldJobCompleted` | [[Field Service Management]] | Creates invoice |
| `StockBelowReorderPoint` | [[Inventory Management]] | Creates draft PO (via [[Purchasing & Procurement]]) |
| `PurchaseOrderApproved` | [[Purchasing & Procurement]] | Creates bill record, updates committed spend |
| `OrderPlaced` | E-commerce | Records revenue |

## Related

- [[Invoicing]]
- [[Expense Management]]
- [[Financial Reporting]]
- [[Accounts Payable & Receivable]]
- [[Bank Reconciliation]]
- [[Budgeting & Forecasting]]
- [[Client Billing & Retainers]]
- [[Panel Map]]
