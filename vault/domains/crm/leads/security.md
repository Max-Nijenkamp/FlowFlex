---
domain: crm
module: leads
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leads — Security

## Permissions

| Permission | Grants |
|---|---|
| `crm.leads.view-any` | View the leads list + records |
| `crm.leads.create` | Create leads |
| `crm.leads.update` | Edit leads |
| `crm.leads.delete` | Delete leads |
| `crm.leads.convert` | Run the "Convert to deal" action |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('crm.leads.view-any')
        && BillingService::hasModule('crm.leads');
}
```

The "Convert to deal" row action is additionally gated on `crm.leads.convert` and hidden once the lead is converted.

## Tenant Isolation

- `crm_leads` carries `company_id` (indexed) via `BelongsToCompany`; the `CompanyScope` global scope constrains all queries.
- Convert resolves the pipeline, stage, contact and deal strictly within the acting company.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('crm.leads')` gates panel access. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Lead PII (email, phone) is stored in plaintext *(assumed — no encryption requirement documented)*. See [[unknowns]].
