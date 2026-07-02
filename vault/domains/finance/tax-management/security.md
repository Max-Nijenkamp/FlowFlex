---
domain: finance
module: tax-management
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tax Management — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.tax.view-any')
            && BillingService::hasModule('finance.tax')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages (`TaxReturnPage`) state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.tax.view` · `finance.tax.manage-rates` · `finance.tax.file-period`

## Integrity controls

- Rates referenced by invoice/bill lines are never hard-deleted (soft-delete only).
- `filePeriod` locks a tax period against rate-affecting recomputation; filing is permission-gated (`finance.tax.file-period`).
- VIES validation is failure-tolerant — a network failure marks a number "unverified" and never blocks a customer/supplier save *(assumed)*.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
