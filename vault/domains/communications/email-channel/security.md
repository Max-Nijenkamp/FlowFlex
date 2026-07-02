---
domain: communications
module: email-channel
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Channel — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.email.manage` | Connect/configure email channels, edit signature |

Messaging is gated by inbox permissions (`comms.inbox.reply`).

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.email.view-any')
        && BillingService::hasModule('comms.email');
}
```

## Encrypted Fields

- `comms_email_channels.oauth_token` — encrypted cast (used by v1.x OAuth connection). See [[../../../architecture/patterns/encryption]].

## Webhook Security (medium — [[../../../build/security-audit-2026-06-11]])

- Inbound webhook **signature-verified**; unknown `inbound_token` dropped.
- **Throttle / rate limiter** on the inbound route.
- Spam-scored mail dropped + logged.

## Upload Contract (medium)

Email attachments: MIME/extension whitelist, max size, tenant-scoped path `companies/{company_id}/comms/...` via `core.files`.

## Tenant Isolation

`comms_email_channels` carries `company_id` (indexed) via `BelongsToCompany`. Inbound resolves company from `inbound_token`, then runs under `WithCompanyContext`. HTML purified before store. See [[../../../security/tenancy-isolation]].

## Related

- [[_module]] · [[../../../security/data-ownership]]
