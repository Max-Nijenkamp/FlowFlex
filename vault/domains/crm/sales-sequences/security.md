---
domain: crm
module: sales-sequences
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sales Sequences — Security

## Permissions

| Permission | Grants |
|---|---|
| crm.sequences.view-any | List/view sequences and enrolments |
| crm.sequences.create | Create sequences |
| crm.sequences.update | Edit sequences and steps |
| crm.sequences.enrol | Enrol contacts/deals |
| crm.sequences.manage-team | Manage team (non-personal) sequences |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.sequences.view-any')
        && hasModule('crm.sequences');
}
```

## Tenant Isolation

All three tables carry `company_id` and are scoped by `CompanyScope`. The advance query filters `(company_id, status, next_step_at)`. Consuming listeners (`DealWon`, `InvoicePaid`) run with `WithCompanyContext` so queued advancement resolves the correct tenant — see [[../../../security/tenancy-isolation]].

## Module Gating

Gated on `crm.sequences` via `hasModule()` in `canAccess()`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.

## Notes

- Rich-text sanitize (medium): HTMLPurifier runs on sequence email-step template HTML on save, consistent with crm.email body purification — prevents stored XSS in outbound step content. See [[../../../security/threat-model]].
