---
domain: crm
module: contracts
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contracts — Security

## Permissions

| Permission | Purpose |
|---|---|
| crm.contracts.view-any | List / view contracts. |
| crm.contracts.view | View a single contract. |
| crm.contracts.create | Create a contract. |
| crm.contracts.send | Move draft → sent. |
| crm.contracts.sign-off | Confirm signature (signed-PDF upload). |
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

## Encrypted Fields

None.

## Source Security Notes

- **Upload contract (medium)** — `markSigned` accepts `application/pdf` only, enforces a max size cap, and stores under `companies/{company_id}/contracts/` via Media Library. See [[../../../security/threat-model]].
