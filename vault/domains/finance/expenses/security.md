---
domain: finance
module: expenses
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Expenses — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.expenses.view-any')
            && BillingService::hasModule('finance.expenses')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. The `ExpenseResource` exposes a "My expenses" tab (own data) and an "All" tab (gated by `view-any`). Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.expenses.view-any` · `finance.expenses.view` · `finance.expenses.submit` · `finance.expenses.approve` · `finance.expenses.reimburse` · `finance.expenses.manage-categories`

`approve` gates both the approve and reject transitions (single approver verb *(assumed)*); `reimburse` gates the reimbursement transition; `manage-categories` gates category CRUD.

## Rate Limiting

Named limiters per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]:

- **Approve** (`finance.expenses.approve`) mutates money (posts a GL entry) and fires `ExpenseApproved` → `panel-action` limiter.
- **Reject** sends a submitter notification (comms) → `panel-action` limiter.
- **Reimburse** (`finance.expenses.reimburse`) mutates money (clears the GL liability) → `panel-action` limiter.
- **CSV export** (payroll / accounting reconciliation) generates a file → `exports` limiter.

See [[../../../architecture/api-design]] and [[../../../architecture/security]].

## Upload contract

Receipts are intended to store under `companies/{company_id}/expense-receipts/` (a Media Library tenant-scoped collection), with a concrete max-size default pinned and a MIME whitelist of `pdf, jpg, png, webp`. Reference the Security upload rules in [[../../../architecture/security]].

## Integrity controls

- An approver must not be the submitter (`CannotApproveOwnExpenseException`).
- Future-dated expenses are rejected.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
