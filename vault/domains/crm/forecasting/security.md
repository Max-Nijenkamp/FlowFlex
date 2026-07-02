---
domain: crm
module: forecasting
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Security

## Permissions

| Permission | Grants |
|---|---|
| `crm.forecasting.view-own` | See own quota and forecast only. |
| `crm.forecasting.view-team` | See team members' quotas and roll-up. |
| `crm.forecasting.manage-quotas` | Create / edit quotas. |
| `crm.forecasting.set-category` | Tag open deals with a forecast category. |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.forecasting.view-any')
        && hasModule('crm.forecasting');
}
```

See [[../../../security/authn-authz]].

## Tenant Isolation

Both tables carry an indexed `company_id` and are scoped via `BelongsToCompany` / `CompanyScope`. Roll-up queries never cross tenants. See [[../../../security/tenancy-isolation]].

## Module Gating

Gated behind the `crm.forecasting` module flag via `hasModule()` in every resource/page `canAccess()`. See [[../../../infrastructure/module-catalog]].

## View Scoping

`view-own` restricts forecasts to `owner_id = auth id`; `view-team` widens to the manager's team members. Enforced in `SalesForecastService` query scoping, not just the UI.

## Encrypted Fields

None.

## Source Security Notes

- Authorization uses spatie/laravel-permission, not policies — see [[../../../security/authn-authz]].
