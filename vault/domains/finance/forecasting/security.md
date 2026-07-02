---
domain: finance
module: forecasting
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.forecasting.view-any')
            && BillingService::hasModule('finance.forecasting')
```

per [[../../../architecture/filament-patterns]] #1 — the custom comparison page (`ForecastComparisonPage`) states it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.forecasting.view-any` · `finance.forecasting.create` · `finance.forecasting.update`

`create` / `update` gate forecast authoring and the seed-from-actuals action; there is no separate approval permission (forecasts are advisory, not posted).

## Integrity controls

- Forecasts never post to the ledger — they are read-only projections, so no immutability/reversal machinery is required.
- Seed-from-actuals reads ledger actuals but does not mutate them.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
