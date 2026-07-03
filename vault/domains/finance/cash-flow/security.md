---
domain: finance
module: cash-flow
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Cash Flow — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.cashflow.view-any')
            && BillingService::hasModule('finance.cashflow')
```

per [[../../../architecture/filament-patterns]] #1 — the custom page (`CashFlowPage`) and the low-cash widget state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

UNVERIFIED: the source spec lists the permission set as `finance.cashflow.view` / `finance.cashflow.manage-items` (no explicit `view-any`); the canonical access-contract phrasing uses `view-any`. The `view-any`/`view` split is unresolved — see [[unknowns]].

## Permissions

`finance.cashflow.view` · `finance.cashflow.manage-items`

`manage-items` gates `AddManualItemAction` (adding/editing manual inflow/outflow items).

## Rate Limiting

- `AddManualItemAction` mutates money (adds/edits cash items) → carries the `panel-action` rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]] and [[../../../architecture/security]].

## Integrity controls

- The nightly rebuild is a full delete + regenerate of projected rows, deterministic and idempotent; actual rows are left untouched.
- Low-cash alerts fire once per breach week (flagged) to avoid re-alerting.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
