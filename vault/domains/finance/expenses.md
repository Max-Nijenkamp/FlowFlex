---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.expenses
status: planned
color: "#4ADE80"
---

# Expenses

Employee expense submission with receipt upload, category tagging, approval workflow, and reimbursement tracking. Feeds into Accounts Payable and the General Ledger.

---

## Core Features

- Expense submission: amount, date, category, merchant, description, receipt upload (via Media Library)
- Expense status: `draft → submitted → approved | rejected → reimbursed`
- Approval workflow: employee → manager → finance
- Expense categories: configurable per company (travel, meals, software, equipment)
- Expense reports: group multiple expenses into a report for bulk approval
- Reimbursement tracking: mark as reimbursed via payroll or bank transfer
- Policy enforcement: flag expenses over configurable limit per category
- Export to CSV for payroll or accounting reconciliation

---

## Data Model

| Table | Key Columns |
|---|---|
| `fin_expenses` | company_id, employee_id, category_id, amount_cents, currency, expense_date, merchant, description, status, approved_by, report_id |
| `fin_expense_categories` | company_id, name, limit_per_transaction_cents, gl_account_code |
| `fin_expense_reports` | company_id, employee_id, title, period_start, period_end, status, submitted_at, approved_at |

---

## Filament

**Nav group:** Expenses

- `ExpenseResource` — list (my expenses / all expenses tab), create, approve/reject actions
- `ExpenseReportResource` — submit grouped expense reports
- `ExpenseCategoryResource` — configure expense categories and limits

---

## Related

- [[domains/finance/accounts-payable]]
- [[domains/finance/general-ledger]]
- [[domains/hr/payroll]] — reimbursement via payroll
