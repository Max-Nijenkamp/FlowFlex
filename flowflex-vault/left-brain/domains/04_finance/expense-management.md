---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: complete
migration_range: 200007
last_updated: 2026-05-12
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Expense Management

Employee expense submission, receipt capture, manager approval flow, and reimbursement tracking. Replaces Expensify, Rydoo, Declaree.

**Panel:** `finance`  
**Phase:** 3  
**Module key:** `finance.expenses`

---

## Data Model

```erDiagram
    expenses {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string category
        decimal amount
        string currency
        string receipt_path
        text description
        date expense_date
        string status
        ulid approved_by FK
        timestamp approved_at
        timestamp rejected_at
        text rejection_reason
        boolean is_reimbursable
        timestamp reimbursed_at
    }
```

**Expense status flow:** `draft` → `submitted` → `approved` → `reimbursed` | `rejected`

**Categories:** Travel, Meals & Entertainment, Software, Office Supplies, Training, Marketing, Other

---

## Service: ExpenseService

```php
createExpense(CreateExpenseData $data): Expense
submitForApproval(Expense $expense): void      // triggers ExpenseSubmitted event
approve(Expense $expense, User $approver): void
reject(Expense $expense, User $approver, string $reason): void
markReimbursed(Expense $expense): void
```

---

## Events

| Event | Trigger | Consumed By |
|---|---|---|
| `ExpenseSubmitted` | submitForApproval() | Notifications (manager approval request) |
| `ExpenseApproved` | approve() | Notifications (employee), Payroll (reimbursement batch), GL (post expense) |
| `ExpenseRejected` | reject() | Notifications (employee with reason) |

---

## GL Integration

On approval → post journal:
- Debit appropriate expense account (e.g. `6400 Travel & Entertainment`)
- Credit `2100 Payroll Liabilities` (reimbursable) or `1000 Bank` (company card)

---

## Permissions

```
finance.expenses.view-own
finance.expenses.submit
finance.expenses.approve
finance.expenses.view-all
finance.expenses.export
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]] — GL posting on approval
- [[invoicing]] — expense reimbursement vs billable expense
