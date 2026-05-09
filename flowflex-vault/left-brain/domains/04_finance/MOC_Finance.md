---
type: moc
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
color: "#059669"
last_updated: 2026-05-08
---

# Finance & Accounting — Map of Content

Complete accounting suite. Invoicing, expenses, AP/AR, bank reconciliation, payroll, tax, budgeting, and advanced financial reporting.

**Panel:** `finance`  
**Phase:** 3 (core) · 6 (extensions)  
**Migration Range:** `200000–249999`  
**Colour:** Emerald `#059669` / Light: `#ECFDF5`  
**Icon:** `heroicon-o-banknotes`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Invoicing | 3 | 📅 planned | Invoice builder, recurring, payment links, PDF |
| Expense Management | 3 | 📅 planned | Receipt capture, approval flow, reimbursements |
| Financial Reporting | 3 | 📅 planned | P&L, balance sheet, cash flow statement |
| Accounts Payable & Receivable | 3 | 📅 planned | Supplier bills, aging reports, reconciliation |
| Bank Reconciliation | 3 | 📅 planned | Bank feeds, auto-match, statement import |
| Budgeting & Forecasting | 3 | 📅 planned | Department budgets, variance analysis |
| Client Billing & Retainers | 3 | 📅 planned | Time-based billing, retainer contracts |
| [[vat-tax-filing\|VAT & Tax Filing]] | 3 | planned | VAT returns, OSS, MTD, multi-jurisdiction tax compliance |
| [[fixed-assets\|Fixed Asset & Depreciation]] | 3 | planned | Asset register, depreciation schedules, disposal journals |
| Subscription & MRR Tracking | 3 | 📅 planned | MRR/ARR dashboard, churn analysis |
| Multi-Currency & FX Management | 6 | planned | Real-time FX, multi-currency reporting |
| Open Banking & Bank Feeds | 3 | planned | PSD2/Plaid bank feeds, auto-match transactions, daily sync |
| [[cash-flow-forecasting\|Cash Flow Forecasting]] | 6 | planned | 13-week rolling forecast, committed spend, scenarios |
| Revenue Recognition | 6 | planned | IFRS 15 / ASC 606, deferred revenue, 5-step model |
| [[credit-control\|Credit Control]] | 3 | planned | Aged debtors, dunning sequences, disputes, payment plans |
| [[corporate-cards-spend-management\|Corporate Cards & Spend Management]] | 6 | planned | Virtual/physical cards, spend controls, AI receipt match |
| [[travel-expense-management\|Travel & Expense Management]] | 6 | planned | Business travel booking, policy enforcement, per diem |
| [[accounts-receivable-automation\|Accounts Receivable Automation]] | 6 | planned | AI dunning, payment prediction, dispute management |
| [[multi-entity-consolidation\|Multi-Entity & Financial Consolidation]] | 6 | planned | Holding companies, subsidiaries, intercompany elimination |
| [[general-ledger-chart-of-accounts\|General Ledger & Chart of Accounts]] | 3 | planned | Double-entry GL, COA, journal entries, period close |
| [[payroll-tax-filing\|Payroll Tax Filing]] | 3 | planned | RTI (UK), Loonaangifte (NL), DSN (FR), 941 (US) auto-submission |
| [[intercompany-billing\|Intercompany Billing]] | 6 | planned | Management fees, cost recharges, transfer pricing |
| [[embedded-payments\|Embedded Payments]] | 4 | planned | Virtual IBANs, SEPA/BACS batch runs, B2B BNPL for instant reconciliation |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `InvoicePaid` | Invoicing | CRM (update deal), Analytics, Marketing (upsell trigger) |
| `InvoiceOverdue` | Invoicing | Notifications, CRM (create task) |
| `ExpenseApproved` | Expenses | Finance (update budget), Payroll (reimbursement) |
| `BillCreated` | AP/AR | Finance (update payables) |
| `PurchaseOrderApproved` | Operations (consumed) | Finance (create bill) |
| `TimeEntryApproved` | Projects (consumed) | Client Billing (mark billable) |
| `FieldJobCompleted` | Operations (consumed) | Invoicing (auto-create invoice) |

---

## Permissions Prefix

`finance.invoices.*` · `finance.expenses.*` · `finance.reports.*`  
`finance.ap-ar.*` · `finance.bank.*` · `finance.budgets.*`  
`finance.payroll.*` · `finance.tax.*`

---

## Competitors Displaced

Xero · QuickBooks · Exact · AFAS · Sage · FreeAgent · Maxio (MRR)

---

## Related

- [[MOC_Domains]]
- [[entity-invoice]]
- [[MOC_HR]] — payroll costs
- [[MOC_CRM]] — deal → invoice trigger
- [[MOC_Operations]] — purchase orders → AP
- [[MOC_Ecommerce]] — checkout → revenue recording
