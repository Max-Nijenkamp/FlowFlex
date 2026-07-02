---
domain: finance
module: expenses
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Expenses — Architecture

Interface→Service binding: `ExpenseServiceInterface` → `ExpenseService`, registered in the Finance service provider.

## State machine

`fin_expenses.status` is a `spatie/laravel-model-states` machine (`ExpenseState`): `draft → submitted → approved | rejected → reimbursed`. Approval and reimbursement carry GL side effects; rejection requires a reason and notifies the submitter. Full transition table in [[features/approval-workflow]]. See [[../../../architecture/patterns/states]].

## Ledger posting

On `approve`, the service posts a balanced journal entry via the ledger (expense account / reimbursable liability), routing through the category's `gl_account_id`. On `markReimbursed`, the liability is cleared. Postings are direct in-domain service calls to [[../general-ledger/_module|finance.ledger]] — no events between same-domain modules.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw float math. The over-limit flag compares `amount_cents` against the category's `limit_per_transaction_cents`. See [[../../../architecture/packages]] (brick/money).

## Receipts & notifications

- Receipts upload via the Media Library, stored under a tenant-scoped collection (see [[security]]).
- Status changes (approved / rejected) send queued notifications via [[../../core/notifications/_module|core.notifications]].
- CSV export supports payroll / accounting reconciliation.

## Events

`ExpenseApproved` fires on approval; the hr.payroll reimbursement listener consumes it (employee-linked expenses only). See [[../../../architecture/event-bus]].

See [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]].
