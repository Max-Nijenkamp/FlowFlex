---
domain: finance
module: expenses
feature: expense-policy
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Expense Policy

Per-category spend limits drive a policy flag.

- Each `fin_expense_categories` row carries an optional `limit_per_transaction_cents` (null = no limit) and a `gl_account_id` posting target.
- On `submit`, the service compares `amount_cents` against the category limit and sets `is_over_limit` when exceeded.
- The flag is advisory: over-limit expenses are flagged for reviewer attention, not blocked *(assumed)*. The `ExpenseResource` surfaces an over-limit badge.
- Limit comparison uses `brick/money`.

## UI
- **Kind**: simple-resource
- **Page**: `fin_expense_categories` CRUD — `/finance/expenses/categories`
- **Layout**: category table (name, `limit_per_transaction_cents`, `gl_account_id`) with create/edit forms; an over-limit badge surfaces on the related `ExpenseResource`.
- **Key interactions**: create/edit a category, set its transaction limit and GL posting target.
- **States**: empty (no categories) · loading (list/form) · error (validation) · selected (category being edited).
- **Gating**: `finance.expenses.manage-categories` *(assumed)*.

## Data
- Owns / writes: `fin_expense_categories` only (`limit_per_transaction_cents` = integer minor units via brick/money; limit comparison uses brick/money).
- Reads: `gl_account_id` references a GL account (own-domain read for the posting target); the advisory `is_over_limit` flag is set on the expense at submit time, not blocked *(assumed)*.
- Cross-domain writes: none — no GL posts here; categories only configure the posting target that approval-workflow later uses via `LedgerService::post` ([[../../../../security/data-ownership]]).

## Relations
- Consumes: no cross-domain events.
- Feeds: no cross-domain events — provides limit + GL-account config consumed in-domain by the expense submit/approval flow.

## Test Checklist

### Unit
- [ ] Over-limit flag set when `amount_cents` exceeds the category `limit_per_transaction_cents` (brick/money); null limit never flags
- [ ] Flag is advisory — an over-limit expense still submits (not blocked) *(assumed)*

### Feature (Pest)
- [ ] On submit, `is_over_limit` is persisted per the category limit comparison; category with no limit leaves it false
- [ ] Category maps to its `gl_account_id`; tenant isolation — company A cannot read/edit company B categories

### Livewire
- [ ] `ExpenseCategoryResource` create/edit validates the limit + GL-account fields; the over-limit badge surfaces on the related `ExpenseResource`
- [ ] `canAccess` / manage requires `finance.expenses.manage-categories`; hidden when `finance.expenses` inactive

See [[../api]], [[../data-model]], [[../architecture]].
