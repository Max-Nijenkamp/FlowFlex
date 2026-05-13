---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.expenses
status: planned
color: "#4ADE80"
---

# Expenses

> Employee expense submissions — upload receipts, categorise, submit for approval, and post approved expenses to the General Ledger.

**Panel:** `finance`
**Module key:** `finance.expenses`

## What It Does

The Expenses module lets employees submit reimbursable expenses incurred on behalf of the company. Each expense claim has a date, amount, currency, category, description, and receipt attachment. Claims are submitted for manager approval. Approved claims are marked for reimbursement and post a journal entry (debit Expense account, credit Accounts Payable) to the General Ledger. Finance sees a consolidated expenses dashboard with total spend by category and department, and can export approved expenses for payroll reimbursement or bank payment.

## Features

### Core
- Expense claim: date, amount, currency, category (travel, meals, software, equipment, other), description, project tag, receipt upload (PDF or image via file-storage module)
- Expense report: group multiple individual claims into a single expense report for submission
- Approval workflow: submitted report routes to employee's direct manager for approval or rejection
- Reimbursement status: pending / approved / reimbursed — finance marks as reimbursed when payment processed
- GL posting: on approval, auto-posts journal — debit Expense category account, credit Accounts Payable

### Advanced
- Category rules: per-category spending limits (e.g. meals max €50/day) — warn submitter and flag to approver if exceeded
- Multi-currency: submit expenses in any currency — converted to base currency at submission date exchange rate
- Mileage claims: enter km/miles and purpose — auto-calculates reimbursement at configured per-km rate
- Duplicate detection: receipt hash comparison — warn when uploading a receipt that appears to have been submitted before
- Bulk export: export approved expense reports as CSV for payroll system or bank payment upload

### AI-Powered
- Receipt OCR: uploaded receipt image parsed by AI — date, merchant, amount, and currency extracted and pre-filled in the expense form
- Policy violation detection: AI cross-checks submitted expenses against company policy (e.g. no alcohol expenses, per diem limits by country) and flags potential violations before submission

## Data Model

```erDiagram
    expense_reports {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        string title
        string status
        decimal total_amount
        string currency
        ulid approved_by FK
        timestamp approved_at
        timestamp reimbursed_at
        timestamps created_at/updated_at
    }

    expense_claims {
        ulid id PK
        ulid expense_report_id FK
        ulid company_id FK
        date expense_date
        decimal amount
        string currency
        decimal base_amount
        decimal fx_rate
        string category
        string description
        string receipt_path
        ulid project_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | draft / submitted / approved / rejected / reimbursed |
| `base_amount` | Amount in company base currency at submission rate |
| `receipt_path` | Media library path to receipt file |

## Permissions

- `finance.expenses.submit-own`
- `finance.expenses.view-own`
- `finance.expenses.approve-team`
- `finance.expenses.view-all`
- `finance.expenses.reimburse`

## Filament

- **Resource:** `ExpenseReportResource`
- **Pages:** `ListExpenseReports`, `CreateExpenseReport`, `ViewExpenseReport` (with claim list and receipt gallery)
- **Custom pages:** None
- **Widgets:** `PendingExpensesWidget` — total value of pending approval reports on finance dashboard
- **Nav group:** Expenses (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Expensify | Employee expense management |
| Spendesk | Spend management and expenses |
| Pleo | Business expense tracking |
| Concur | Corporate travel and expense |

## Related

- [[general-ledger]]
- [[accounts-payable]]
- [[bank-accounts]]
- [[tax-management]]
- [[budgets]]
