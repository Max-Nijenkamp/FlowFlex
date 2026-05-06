---
tags: [flowflex, domain/finance, overview, phase/3]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-06
---

# Finance Overview

The financial nerve system of the business. From invoices to payroll costs, from bank reconciliation to VAT returns. Every number in the platform that involves money routes through here.

**Filament Panel:** `finance`
**Domain Colour:** Emerald `#059669` / Light: `#D1FAE5`
**Domain Icon:** `banknotes` (Heroicons)
**Phase:** 3 (core: Invoicing, Expenses, Financial Reporting) + 5 (full suite)

## Modules in This Domain

| Module | Phase | Description |
|---|---|---|
| [[Invoicing]] | 3 | Invoice builder, auto-generate from time/milestones |
| [[Expense Management]] | 3 | Mobile receipt, OCR, approval, payroll reimbursement |
| [[Financial Reporting]] | 3 | P&L, balance sheet, cash flow, custom reports |
| [[Accounts Payable & Receivable]] | 5 | Supplier bills, payment runs, aged reports |
| [[Bank Reconciliation]] | 5 | Open banking, auto-match, manual reconciliation |
| [[Budgeting & Forecasting]] | 5 | Department budgets, actuals, variance, scenarios |
| [[Client Billing & Retainers]] | 5 | Time-to-invoice, retainer management |
| [[Tax & VAT Compliance]] | 5 | VAT/GST, MTD, multi-jurisdiction |
| [[Fixed Asset & Depreciation]] | 5 | Asset register, depreciation schedules |
| [[Subscription & MRR Tracking]] | 5 | MRR/ARR, churn, revenue recognition |

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
