---
domain: finance
module: financial-reporting
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Financial Reporting — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.reporting.view-any')
            && BillingService::hasModule('finance.reporting')
```

per [[../../../architecture/filament-patterns]] #1 — the three custom report pages (`ProfitLossPage`, `BalanceSheetPage`, `CashFlowStatementPage`) state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

UNVERIFIED: the source spec lists the permission set as `finance.reporting.view` / `finance.reporting.export`, while the canonical access-contract phrasing uses `view-any`. The `view-any`/`view` split is unresolved — see [[unknowns]].

## Permissions

`finance.reporting.view` · `finance.reporting.export`

`export` gates the Excel/PDF export actions on each report.

## Rate Limiting

- **`exports` rate limiter** (named, per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]] and [[../../../architecture/security]]): the Excel/PDF export actions on all three report pages carry the `exports` limiter to prevent export abuse / resource exhaustion.

## Integrity & abuse controls

- Report export actions are gated on `finance.reporting.export` and rate-limited via the named `exports` limiter (see above).
- **Balance assertion**: the balance sheet asserts assets = liabilities + equity; an imbalance raises a Sentry alarm rather than rendering silently corrupt figures.
- The module owns no tables and never writes to the ledger — it is read-only over source data.
- Tenant isolation inherited from source-table `company_id` scope — see [[../../../security/tenancy-isolation]].

No encrypted fields in this module.
