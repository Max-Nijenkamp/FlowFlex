---
domain: finance
module: multi-currency
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Multi-Currency — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.currency.view-any')
            && BillingService::hasModule('finance.currency')
```

per [[../../../architecture/filament-patterns]] #1 — the FX gain/loss report custom page states it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.currency.view` · `finance.currency.manage`

## Integrity controls

- The GL never contains foreign-currency amounts — conversion to base currency happens before posting.
- Exchange rates are immutable history (new effective-dated rows rather than edits); rate lookup is most-recent ≤ date.
- `RevalueOpenBalancesCommand` is idempotent per (period, currency) via a unique guard.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
