---
domain: finance
module: budgets
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Budgets — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.budgets.view-any')
            && BillingService::hasModule('finance.budgets')
```

per [[../../../architecture/filament-patterns]] #1 — the custom variance page (`BudgetVariancePage`) and the over-budget widget state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.budgets.view-any` · `finance.budgets.create` · `finance.budgets.update` · `finance.budgets.approve`

`approve` gates the draft → approved transition; once approved, lines are immutable and edits require `revise()` (a new version).

## Integrity controls

- Approved budget lines are immutable — in-year changes create a new version row, leaving the approved version auditable *(assumed)*.
- Variance alerts fire once per (budget, period) over threshold (flag column) to avoid re-alerting.
- `remaining()` is read-only and tenant-scoped; procurement/workforce callers cannot mutate budgets through it.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
