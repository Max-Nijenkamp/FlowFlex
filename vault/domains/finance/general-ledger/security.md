---
domain: finance
module: general-ledger
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# General Ledger — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.ledger.view-any')
            && BillingService::hasModule('finance.ledger')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages (`TrialBalancePage`) state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.ledger.view-any` · `finance.ledger.view` · `finance.ledger.post-manual` · `finance.ledger.reverse` · `finance.ledger.manage-accounts` · `finance.ledger.close-period`

## Integrity controls

- Posted entries are immutable; there is no update/delete path — only reversals.
- `closePeriod` / `reopenPeriod` are owner-level and audited.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
