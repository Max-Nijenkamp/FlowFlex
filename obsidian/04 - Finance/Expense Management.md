---
tags: [flowflex, domain/finance, expenses, phase/3]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: built
last_updated: 2026-05-06
---

# Expense Management

Employee expense submission, approval, and reimbursement — fully connected to payroll.

**Who uses it:** All employees (submit), managers (approve), finance team (process)
**Filament Panel:** `finance`
**Depends on:** [[Employee Profiles]], [[Payroll]] (for reimbursement)
**Phase:** 3
**Build complexity:** High — 2 resources, 2 pages, 4 tables

## Events Fired

- `ExpenseSubmitted`
- `ExpenseApproved` → consumed by [[Payroll]] (add reimbursement to next pay run)
- `ExpenseRejected`

## Features

- **Receipt upload** — mobile camera or file upload
- **OCR receipt scanning** — auto-fill date, vendor, amount from photo (queued job)
- **Expense categories** — configurable: Travel, Accommodation, Meals, Equipment, etc.
- **Mileage tracking** — distance + rate per mile/km = expense amount
- **Per diem rates** — daily allowance by destination country
- **Expense report grouping** — group multiple expenses into one report for submission
- **Approval workflow** — line manager approves, finance reviews above threshold
- **Multi-currency expenses** — employee paid in foreign currency, converted to base
- **Policy enforcement** — flag expenses over category limits before approval
- **Reimbursement via payroll** — approved expenses added to next pay run automatically
- **Finance export** — approved expenses to accounting journal

## Database Tables (4)

1. `expenses` — individual expense records
2. `expense_reports` — grouped submission records
3. `expense_categories` — configured category definitions per tenant
4. `mileage_rates` — per km/mile rates per territory

## Related

- [[Finance Overview]]
- [[Payroll]]
- [[Employee Profiles]]
- [[Accounts Payable & Receivable]]
