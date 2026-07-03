---
domain: events
module: sponsors
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sponsors — Security

## Permissions

| Permission | Grants |
|---|---|
| `events.sponsors.view-any` | View sponsors + records |
| `events.sponsors.manage` | Create/edit sponsors + deliverables; create invoice |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.sponsors.view-any')
        && BillingService::hasModule('events.sponsors');
}
```

The create-invoice action is additionally hidden when `finance.invoicing` is inactive.

## Uploads

- Sponsor logo: allowed image MIME whitelist, max file size, `companies/{id}/` media path (per [[../../../_archive/build-history/security-audit-2026-06-11]], medium).

## Tenant Isolation

- Both tables carry `company_id` (indexed); `CompanyScope` constrains queries. The CRM contact + Finance invoice references resolve strictly within the acting company. See [[../../../security/tenancy-isolation]].

## Cross-Domain Boundaries

- `contact_id` is a **read** reference into CRM; `fin_invoice_id` is set only from the value returned by the Finance service. Sponsors never writes CRM or Finance tables. See [[../../../security/data-ownership]].

## Encrypted Fields

None. Sponsor name/logo is public-facing; amounts are business (not personal) data.
