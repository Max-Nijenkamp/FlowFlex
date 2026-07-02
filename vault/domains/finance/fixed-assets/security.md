---
domain: finance
module: fixed-assets
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Fixed Assets — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.assets.view-any')
            && BillingService::hasModule('finance.assets')
```

per [[../../../architecture/filament-patterns]] #1 — the custom page (`DepreciationRunPage`) states it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.assets.view-any` · `finance.assets.create` · `finance.assets.update` · `finance.assets.run-depreciation` · `finance.assets.dispose`

`run-depreciation` and `dispose` are the privileged write operations — both post irreversible entries to the ledger.

## Integrity controls

- Disposal is one-way: a disposed asset rejects a second disposal (`AlreadyDisposedException`).
- Every depreciation/disposal posts a balanced GL entry; the posted `journal_entry_id` is recorded for audit traceability.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
