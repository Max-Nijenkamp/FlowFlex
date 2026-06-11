---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.expenses
status: complete
priority: v1-core
depends-on: [finance.ledger, core.billing, core.rbac, core.files, core.notifications]
soft-depends: [hr.profiles, hr.payroll, finance.ap]
fires-events: [ExpenseApproved]
consumes-events: []
patterns: [states, money, events]
tables: [fin_expenses, fin_expense_categories, fin_expense_reports]
permission-prefix: finance.expenses
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Expenses

Employee expense submission with receipt upload, category tagging, approval workflow, and reimbursement tracking. Feeds into Accounts Payable and the General Ledger.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | approved expenses post to GL (category → account) |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, receipts, status mails |
| Soft | [[domains/hr/employee-profiles\|hr.profiles]] | submitter linked to employee; user-only otherwise *(assumed: employee_id nullable → user_id always)* |
| Soft | [[domains/hr/payroll\|hr.payroll]] | consumes `ExpenseApproved` for reimbursement |
| Soft | [[domains/finance/accounts-payable\|finance.ap]] | non-employee reimbursement path |

---

## Core Features

- Expense submission: amount, date, category, merchant, description, receipt upload (via Media Library)
- Expense status: `draft → submitted → approved | rejected → reimbursed`
- Approval workflow: employee → manager → finance *(v1: single approver with `finance.expenses.approve` — chain hook later *(assumed)*)*
- Expense categories: configurable per company (travel, meals, software, equipment) — each maps to a GL account
- Expense reports: group multiple expenses into a report for bulk approval
- Reimbursement tracking: mark as reimbursed via payroll or bank transfer
- Policy enforcement: flag expenses over configurable limit per category (flag, not block *(assumed)*)
- Export to CSV for payroll or accounting reconciliation

---

## Data Model

### fin_expenses

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| user_id | ulid | not null FK users | submitter |
| employee_id | ulid | nullable FK hr_employees | when HR active |
| category_id | ulid | not null FK | |
| amount_cents | bigint | > 0 | |
| currency | string(3) | not null | |
| expense_date | date | not null, not future | |
| merchant | string | not null | |
| description | text | nullable | |
| status | string | default `draft` | state machine |
| is_over_limit | boolean | default false | policy flag |
| approved_by | ulid nullable | | |
| report_id | ulid nullable FK | | |
| reimbursed_via | string nullable | payroll / bank-transfer | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status)`, `(company_id, user_id)`

### fin_expense_categories

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | unique per company |
| limit_per_transaction_cents | bigint nullable | null = no limit |
| gl_account_id | ulid FK fin_accounts | posting target |
| deleted_at | timestamp nullable | |

### fin_expense_reports

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), user_id FK | ulid | |
| title | string | |
| period_start / period_end | date | |
| status | string default `draft` | draft / submitted / approved / rejected |
| submitted_at / approved_at | timestamp nullable | |

---

## State Machine

Column: `fin_expenses.status` — `ExpenseState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `submitted` | submitter | receipt required *(assumed: configurable per category)* |
| `submitted` | `approved` | `finance.expenses.approve` (≠ submitter) | fires `ExpenseApproved`; GL posting (expense account / reimbursable liability) |
| `submitted` | `rejected` | `finance.expenses.approve` | reason required; notification |
| `approved` | `reimbursed` | payroll listener confirm or manual `finance.expenses.reimburse` | liability cleared in GL |

Audited.

---

## DTOs

### SubmitExpenseData
| Field | Type | Validation |
|---|---|---|
| category_id | string | required, exists in company |
| amount_cents | int | min:1 |
| expense_date | CarbonImmutable | required, before_or_equal:today |
| merchant | string | required, max:200 |
| description | ?string | max:1000 |
| receipt | ?UploadedFile | mimes:pdf,jpg,png,webp, max per settings; required per category flag |

### RejectExpenseData — expense_id, reason (required, max:1000)

## Services & Actions

Interface→Service: `ExpenseServiceInterface` → `ExpenseService`.

- `submit(SubmitExpenseData $data): ExpenseData` — sets over-limit flag from category
- `approve(string $expenseId): ExpenseData` — fires `ExpenseApproved`, posts GL entry; throws `CannotApproveOwnExpenseException`
- `reject(RejectExpenseData $data): ExpenseData`
- `markReimbursed(string $expenseId, string $via): ExpenseData`
- `submitReport(string $reportId): void` — bulk-submits contained drafts

## Events

### Fires: ExpenseApproved
| Payload field | Type |
|---|---|
| company_id | string |
| expense_id | string |
| employee_id | ?string |
| amount_cents | int |
| currency | string |

Consumer: hr.payroll reimbursement line (employee_id non-null only) — [[architecture/event-bus]].

---

## Filament

**Nav group:** Expenses

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ExpenseResource` | #1 CRUD resource | tabs: My expenses / All (permission-gated); approve/reject actions; over-limit badge |
| `ExpenseReportResource` | #1 CRUD resource | grouped submission |
| `ExpenseCategoryResource` | #1 CRUD resource | category + limit + GL account |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('finance.expenses.view-any') && BillingService::hasModule('finance.expenses')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): State that receipts store under companies/{company_id}/expense-receipts/ (Media Library tenant-scoped collection) and pin a concrete max size default.

---

## Permissions

`finance.expenses.view-any` · `finance.expenses.view` · `finance.expenses.submit` · `finance.expenses.approve` · `finance.expenses.reimburse` · `finance.expenses.manage-categories`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Own-data: submitter sees own expenses; `view-any` sees all
- [ ] Approve fires `ExpenseApproved` with contract payload + posts balanced GL entry
- [ ] Approver ≠ submitter enforced
- [ ] Over-limit flag set when exceeding category limit
- [ ] Future-dated expense rejected
- [ ] Reject requires reason, notifies submitter
- [ ] Report bulk-submit transitions all contained drafts

---

## Build Manifest

```
database/migrations/xxxx_create_fin_expense_categories_table.php
database/migrations/xxxx_create_fin_expenses_table.php
database/migrations/xxxx_create_fin_expense_reports_table.php
app/Models/Finance/{Expense,ExpenseCategory,ExpenseReport}.php
app/States/Finance/Expense/{ExpenseState,Draft,Submitted,Approved,Rejected,Reimbursed}.php
app/Data/Finance/{SubmitExpenseData,RejectExpenseData,ExpenseData}.php
app/Contracts/Finance/ExpenseServiceInterface.php
app/Services/Finance/ExpenseService.php
app/Exceptions/Finance/CannotApproveOwnExpenseException.php
app/Events/Finance/ExpenseApproved.php
app/Filament/Finance/Resources/{ExpenseResource,ExpenseReportResource,ExpenseCategoryResource}.php
database/factories/Finance/{ExpenseFactory,ExpenseCategoryFactory,ExpenseReportFactory}.php
tests/Feature/Finance/{ExpenseApprovalTest,ExpensePolicyTest,ExpenseGlPostingTest}.php
```

---

## Related

- [[domains/finance/accounts-payable]]
- [[domains/finance/general-ledger]]
- [[domains/hr/payroll]] — reimbursement via payroll
- [[architecture/event-bus]]
