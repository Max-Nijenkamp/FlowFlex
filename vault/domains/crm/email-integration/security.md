---
domain: crm
module: email-integration
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Integration — Security

## Permissions

| Permission | Grants |
|---|---|
| `crm.email.connect-own` | Connect/disconnect one's own mailbox |
| `crm.email.send` | Send tracked emails |
| `crm.email.view-shared` | View shared emails on timelines/threads |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('crm.email.view-any')
        && BillingService::hasModule('crm.email');
}
```

**Visibility scope:** private emails are readable only by the connection owner — not even by holders of `view-any`. Enforced via a query scope on `crm_emails`.

## Tenant Isolation

- Both tables carry `company_id` (indexed) via `BelongsToCompany` under `CompanyScope`.
- Sync, send and thread queries are constrained to the acting company.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('crm.email')` gates panel access. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

| Field | Notes |
|---|---|
| `crm_email_connections.oauth_token` | Encrypted blob holding access + refresh tokens. |

See [[../../../security/encryption]] and [[../../../architecture/patterns/encryption]].

## Security Notes

### Public/portal guard — HIGH
Tracking endpoints (`TrackOpenController`, `TrackClickController`) run on a **guest** route group with no app session. They must validate the per-email token signature and stay isolated from authenticated guards. See [[../../../security/authn-authz]].

### Rate limiter — medium
Apply a named rate limiter to the tracking pixel + click-redirect. Constrain the click redirect to validated **stored** URLs only (no open redirect).

### Webhook / OAuth verification — HIGH
Require signature/state verification on the OAuth callback (`state` + PKCE) and on any provider push webhook **before** processing. See [[../../../security/webhooks-signing]].

### GDPR
Emails of an erased contact are unlinked + body purged *(assumed — personal correspondence)*. See [[../../../security/data-privacy-gdpr]].
