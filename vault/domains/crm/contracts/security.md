---
domain: crm
module: contracts
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Contracts — Security

## Permissions

| Permission | Purpose |
|---|---|
| crm.contracts.view-any | List / view contracts. |
| crm.contracts.view | View a single contract. |
| crm.contracts.create | Create a contract (incl. create-from-deal). |
| crm.contracts.update | Edit a draft contract. |
| crm.contracts.delete | Soft-delete a contract. |
| crm.contracts.send | Move draft → sent (dispatch to customer). |
| crm.contracts.sign-off | Confirm signature (signed-PDF upload). |
| crm.contracts.renew | Renew an active contract (`ContractService::renew`). |
| crm.contracts.terminate | Terminate an active contract. |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.contracts.view-any')
        && hasModule('crm.contracts');
}
```

## Tenant Isolation

All rows carry `company_id` and are scoped via `BelongsToCompany` / `CompanyScope`. See [[../../../security/tenancy-isolation]].

## Module Gating

Gated behind `crm.contracts` in [[../../../infrastructure/module-catalog]].

## Rate Limiting

- **Send / sign-off panel actions (MEDIUM)** — `crm.contracts.send` dispatches the contract to the customer and sign-off generates/renders the signed PDF (spatie/laravel-pdf); both run under the default `panel-action` limiter (comms + file-generation categories) per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]] *(assumed limiter name)*.
- **Renewal alerts** — pre-expiry notifications are dispatched by `ContractLifecycleCommand` through core.notifications (queued), not a user-triggered endpoint.

## Encrypted Fields

None.

## Source Security Notes

- **Upload contract (medium)** — `markSigned` accepts `application/pdf` only, enforces a max size cap, and stores under `companies/{company_id}/contracts/` via Media Library. See [[../../../security/threat-model]].
