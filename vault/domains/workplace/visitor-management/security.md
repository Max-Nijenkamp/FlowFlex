---
domain: workplace
module: visitor-management
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Visitor Management — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.visitors.view-any` | View the visitor log + records |
| `workplace.visitors.pre-register` | Register an expected visitor (all users) |
| `workplace.visitors.manage` | Full CRUD, check-in/out on behalf, log export |
| `workplace.visitors.kiosk` | Kiosk self-service check-in (kiosk role only) |

**Verb / lifecycle step → permission** (per the frozen [[../../../_meta/spec-template]] verb-per-command rule):

| Command / lifecycle step | Permission |
|---|---|
| Pre-register an expected visitor | `workplace.visitors.pre-register` |
| Kiosk self check-in (assign badge, notify host) | `workplace.visitors.kiosk` |
| Check-in / check-out on behalf | `workplace.visitors.manage` |
| Log export | `workplace.visitors.manage` |
| PII purge (`PurgeVisitorsCommand`) | system command — no user permission |

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
- **Pre-registration + check-in** dispatch comms (visitor confirmation mail, host arrival mail + in-app ping), so these panel actions additionally carry the named `panel-action` rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. Prevents mail/notification flooding.

## Tenant Isolation

- `wp_visitors` carries `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries. Hosts + the kiosk session are company-scoped.

See [[../../../security/tenancy-isolation]].

## GDPR / Retention

- Visitor PII is purged after 12 months via `PurgeVisitorsCommand` *(assumed retention)*. See [[../../../architecture/data-lifecycle]].

## Module Gating

`BillingService::hasModule('workplace.visitors')`. See [[../../../infrastructure/module-catalog]].
