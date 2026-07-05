---
domain: finance
module: expenses
type: module
module-key: finance.expenses
priority: v1-core
build-status: in-progress
status: wip
depends-on: [finance.ledger, core.billing, core.rbac, core.files, core.notifications]
soft-depends: [hr.profiles, hr.payroll, finance.ap]
fires-events: [ExpenseApproved]
consumes-events: []
patterns: [states, money, events]
tables: [fin_expenses, fin_expense_categories, fin_expense_reports]
permission-prefix: finance.expenses
encrypted-fields: []
color: "#4ADE80"
updated: 2026-07-05
---

# Expenses

Employee expense submission with receipt upload, category tagging, approval workflow, and reimbursement tracking. Feeds into Accounts Payable and the General Ledger.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Module-key

`finance.expenses`

**Priority:** v1-core  
**Panel:** finance  
**Permission prefix:** `finance.expenses`  
**Tables:** `fin_expenses`, `fin_expense_categories`, `fin_expense_reports`

## Purpose

Employees submit expenses with receipts; approvers (never the submitter) approve or reject them. Each approval is intended to fire `ExpenseApproved` and post a balanced journal entry (expense account / reimbursable liability) through the ledger. Reimbursement clears the liability, via payroll or bank transfer.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | approved expenses post to GL (category → account) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/file-storage/_module\|core.files]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, receipts, status mails |
| Soft | [[../../hr/employee-profiles/_module\|hr.profiles]] | submitter linked to employee; user-only otherwise *(assumed: employee_id nullable → user_id always)* |
| Soft | [[../../hr/payroll/_module\|hr.payroll]] | consumes `ExpenseApproved` for reimbursement |
| Soft | [[../accounts-payable/_module\|finance.ap]] | non-employee reimbursement path |

## Core Features

- Expense submission: amount, date, category, merchant, description, receipt upload (via Media Library).
- Expense status: `draft → submitted → approved | rejected → reimbursed` — see [[features/approval-workflow]].
- Approval workflow: employee → manager → finance *(v1: single approver with `finance.expenses.approve` — chain hook later *(assumed)*)*.
- Expense categories: configurable per company (travel, meals, software, equipment) — each maps to a GL account; see [[features/expense-policy]].
- Expense reports: group multiple expenses into a report for bulk approval — see [[features/expense-reports]].
- Reimbursement tracking: mark as reimbursed via payroll or bank transfer.
- Policy enforcement: flag expenses over a configurable limit per category (flag, not block *(assumed)*).
- Export to CSV for payroll or accounting reconciliation.

## Permissions

`finance.expenses.view-any` · `finance.expenses.view` · `finance.expenses.submit` · `finance.expenses.approve` · `finance.expenses.reimburse` · `finance.expenses.manage-categories`

## Test Checklist

- [ ] Tenant isolation: company A cannot see, approve, or reimburse company B expenses/reports/categories
- [ ] Module gating: artifacts hidden when `finance.expenses` inactive
- [ ] Own-data: submitter sees own expenses; `view-any` sees all
- [ ] Approve fires `ExpenseApproved` with contract payload + posts balanced GL entry
- [ ] Approver ≠ submitter enforced
- [ ] Over-limit flag set when exceeding category limit
- [ ] Future-dated expense rejected
- [ ] Reject requires reason, notifies submitter
- [ ] Report bulk-submit transitions all contained drafts

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

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_expenses`, `fin_expense_categories`, `fin_expense_reports`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Fires | `ExpenseApproved` → reimbursement | [[../../hr/payroll/_module\|hr.payroll]] |
| Calls | `LedgerService::post` on approval | [[../general-ledger/_module\|finance.ledger]] |
| Reads | employee (soft) | [[../../hr/employee-profiles/_module\|hr.profiles]] |
| Reads | tax rates | [[../tax-management/_module\|finance.tax]] |

## Entity Notes

- [[architecture]] — service, state machine, money, GL posting, uploads
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, upload contract
- [[decisions]] — single-approver v1, employee/user linkage
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/approval-workflow]], [[features/expense-reports]], [[features/expense-policy]]

## Related

- [[../accounts-payable/_module]]
- [[../general-ledger/_module]]
- [[../../hr/payroll/_module]] — reimbursement via payroll
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
