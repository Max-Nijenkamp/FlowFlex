---
domain: finance
module: expenses
feature: approval-workflow
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Approval Workflow (State Machine)

Column: `fin_expenses.status` — `ExpenseState` (`spatie/laravel-model-states`).

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `submitted` | submitter | receipt required *(assumed: configurable per category)* |
| `submitted` | `approved` | `finance.expenses.approve` (≠ submitter) | fires `ExpenseApproved`; GL posting (expense account / reimbursable liability) |
| `submitted` | `rejected` | `finance.expenses.approve` | reason required; notification |
| `approved` | `reimbursed` | payroll listener confirm or manual `finance.expenses.reimburse` | liability cleared in GL |

- v1 is a single approver step; the employee → manager → finance chain is a later hook *(assumed)*.
- The approver must not be the submitter (`CannotApproveOwnExpenseException`).
- Audited.

## UI
- **Kind**: simple-resource
- **Page**: `ExpenseResource` — `/finance/expenses`
- **Layout**: expense table + detail view exposing submit / approve / reject / reimburse actions driven by `ExpenseState`.
- **Key interactions**: submit (draft→submitted); approve/reject (submitted→approved/rejected, reason required on reject); reimburse (approved→reimbursed).
- **States**: empty (no expenses) · loading (list/action) · error (`CannotApproveOwnExpenseException`, or reject-without-reason) · selected (expense with state-appropriate actions).
- **Gating**: approve → `finance.expenses.approve` (approver ≠ submitter); reimburse → `finance.expenses.reimburse`.

## Data
- Owns / writes: `fin_expenses` only (amount_cents = integer minor units via brick/money).
- Reads: own tables; category limits/GL targets from expense-policy (own domain).
- Cross-domain writes: on approve, posts a GL entry (expense account / reimbursable liability) via `LedgerService::post` — never writes `fin_journal_*` directly ([[../../../../security/data-ownership]]).

## Relations
- Consumes: payroll listener confirm can drive `approved→reimbursed` (reimbursement paid via hr.payroll).
- Feeds: fires `ExpenseApproved` on approval → consumed by hr.payroll for reimbursement. v1 is single-approver *(assumed)*; the employee → manager → finance chain is a later hook.

## Test Checklist

### Unit
- [ ] State machine: legal transitions accepted (`draft→submitted`, `submitted→approved|rejected`, `approved→reimbursed`); illegal ones rejected
- [ ] Reject without a reason is rejected

### Feature (Pest)
- [ ] Approve by a non-submitter posts a balanced GL entry (expense account / reimbursable liability) via `LedgerService::post` and fires `ExpenseApproved` with the contract payload
- [ ] Approver = submitter throws `CannotApproveOwnExpenseException`
- [ ] Reject records the reason and notifies the submitter; reimburse clears the liability; tenant isolation on every transition

### Livewire
- [ ] `ExpenseResource` submit/approve/reject/reimburse actions are gated by their permissions and hidden for the submitter on their own record
- [ ] `canAccess` denied without `finance.expenses.view-any` and when `finance.expenses` inactive

See [[../api]], [[../architecture]], [[../../../../architecture/patterns/states]].
