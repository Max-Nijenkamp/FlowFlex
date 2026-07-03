---
domain: finance
module: expenses
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Expenses

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ExpenseResource` | #1 CRUD resource | tweaks: state-badge-column (expense state + submit/approve/reject/reimburse actions), custom-header-actions (submit / approve / reject / reimburse / CSV export) | "My expenses" (own data) + "All" (`view-any`) list tabs; over-limit badge; receipt upload via Media Library |
| `ExpenseReportResource` | #1 CRUD resource | tweaks: state-badge-column (report status), custom-header-actions (bulk-submit / CSV export) | groups member expenses; bulk-submit cascades to member drafts |
| `ExpenseCategoryResource` | #1 CRUD resource | — | per-company categories; `limit_per_transaction_cents` + `gl_account_id` posting target |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.expenses.view-any') && BillingService::hasModule('finance.expenses')`
per [[../../../architecture/filament-patterns]] #1. The "My expenses" tab additionally scopes to own records; approve/reject/reimburse header actions each gate on their own permission (approver ≠ submitter). No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Expense / report / category CRUD (draft, form/API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Submit (draft → submitted; over-limit flag) | Pessimistic | `DB::transaction()` + `lockForUpdate()` — state transition per [[../../../architecture/patterns/states]] |
| Approve (money; posts GL entry, fires `ExpenseApproved`) | Pessimistic | `DB::transaction()` + `lockForUpdate()`; re-read state, enforce approver ≠ submitter, `LedgerService::post` |
| Reject (state transition; reason required) | Pessimistic | `DB::transaction()` + `lockForUpdate()` state transition per [[../../../architecture/patterns/states]] |
| Reimburse (money; clears reimbursable liability in GL) | Pessimistic | `DB::transaction()` + `lockForUpdate()`; `LedgerService::post` to clear liability |
| Report bulk-submit (cascade to member drafts) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on members; all contained drafts transition atomically |
| Expense list / detail / CSV export | n-a | read-only / derived — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/interface-service]], [[data-model]], [[api]].
