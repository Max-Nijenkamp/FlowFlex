---
tags: [flowflex, domain/finance, accounts-payable, accounts-receivable, phase/8]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-06
---

# Accounts Payable & Receivable

Full AP and AR management. Track what you owe and what you're owed.

**Who uses it:** Finance team
**Filament Panel:** `finance`
**Depends on:** Core
**Phase:** 8

## Events Fired

- `BillReceived`
- `BillPaid`
- `PaymentRunCompleted`

## Events Consumed

- `PurchaseOrderApproved` (from [[Purchasing & Procurement]]) → creates bill record, updates committed spend

## Features

- **Supplier bill entry** — upload bill, enter details, code to account and department
- **Bill approval workflow** — approve bills above a threshold
- **Payment run** — batch-approve bills for payment, export payment file
- **BACS/SEPA/ACH payment file export**
- **Supplier statement reconciliation**
- **Aged creditor report** — what we owe, by how many days
- **Aged debtor report** — what's owed to us, by how many days
- **Purchase orders** — raise PO before bill received — 3-way match: PO → receipt → bill
- **Automated payment reminder sequences** — Day 0, Day 7, Day 14, Day 30 overdue
- **Dispute management** — mark an invoice as disputed, log reason, track resolution

## Related

- [[Finance Overview]]
- [[Invoicing]]
- [[Bank Reconciliation]]
- [[Purchasing & Procurement]]
- [[Budgeting & Forecasting]]
