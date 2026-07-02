---
domain: finance
module: accounts-receivable
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Receivable — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.ar.view-any')
            && BillingService::hasModule('finance.ar')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages (`ArAgingPage`, `CustomerStatementPage`) state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.ar.view-any` · `finance.ar.view` · `finance.ar.manage-dunning` · `finance.ar.write-off` · `finance.ar.allocate-payment`

## Integrity controls

- Write-off is permission-gated (`finance.ar.write-off`) and records the approving user on `fin_ar_writeoffs.approved_by`.
- Payment allocation validates that the allocations sum to the payment amount and that no allocation exceeds an invoice's open balance.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
