---
domain: crm
module: revenue-intelligence
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Revenue Intelligence — Security

## Permissions

| Permission | Purpose |
|---|---|
| crm.revenue-intelligence.view | View deal health, at-risk queue, win/loss analysis, and dashboards. |
| crm.revenue-intelligence.recalculate | Manually trigger a deal-health recalculation (admin) *(assumed)*. |

A single view permission gates the whole module; the access contract references `view-any` as the panel-level convention alias.

**Rate limiting:** the manual recalculation trigger (`crm.revenue-intelligence.recalculate`, a bulk recompute over open deals) runs behind the named `panel-action` rate limiter. Per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.revenue-intelligence.view-any')
        && hasModule('crm.revenue-intelligence');
}
```

## Tenant Isolation

`crm_deal_health` and `crm_win_loss` carry `company_id` and are scoped via `BelongsToCompany` / `CompanyScope`. All reads of `crm_deals` / `crm_activities` inherit the company scope. See [[../../../security/tenancy-isolation]].

## Module Gating

Gated behind `crm.revenue-intelligence` in [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
