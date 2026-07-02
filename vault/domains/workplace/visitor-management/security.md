---
domain: workplace
module: visitor-management
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Visitor Management — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.visitors.view-any` | View the visitor log + records |
| `workplace.visitors.pre-register` | Register an expected visitor (all users) |
| `workplace.visitors.manage` | Full CRUD, check-in/out on behalf, log export |
| `workplace.visitors.kiosk` | Kiosk self-service check-in (kiosk role only) |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.visitors.view-any')
        && BillingService::hasModule('workplace.visitors');
}
```

The kiosk page gates on `workplace.visitors.kiosk`.

## Encrypted Fields (external PII)

- `wp_visitors.name` and `wp_visitors.email` are **encrypted** (`encrypted` cast, `text` columns). This is the only Workplace module holding **external-person PII**, hence the module retains its `encrypted-fields` frontmatter. See [[../../../security/encryption]].
- Encrypted columns are not plaintext-searchable; kiosk name lookup decrypts today's expected set in memory *(assumed)* — see [[unknowns]].

## Rate Limiting

- **Kiosk check-in + lookup** actions are **rate-limited** per device session / IP (security audit 2026-06-11, medium). Prevents enumeration of expected visitors via the lookup field.

## Tenant Isolation

- `wp_visitors` carries `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries. Hosts + the kiosk session are company-scoped.

See [[../../../security/tenancy-isolation]].

## GDPR / Retention

- Visitor PII is purged after 12 months via `PurgeVisitorsCommand` *(assumed retention)*. See [[../../../architecture/data-lifecycle]].

## Module Gating

`BillingService::hasModule('workplace.visitors')`. See [[../../../infrastructure/module-catalog]].
