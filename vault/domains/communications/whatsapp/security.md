---
domain: communications
module: whatsapp
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# WhatsApp — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.whatsapp.manage-config` | Connect number, enter/update credentials |
| `comms.whatsapp.manage-templates` | Create/submit templates, track approval |

Messaging itself is gated by the inbox permissions (`comms.inbox.reply`).

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.whatsapp.view-any')
        && BillingService::hasModule('comms.whatsapp');
}
```

## Encrypted Fields

- `comms_whatsapp_config.api_key` — encrypted cast, write-only in UI, never re-displayed.
- `comms_whatsapp_config.webhook_secret` — encrypted cast (provider verify token).

See [[../../../architecture/patterns/encryption]].

## Webhook Security (medium — [[../../../build/security-audit-2026-06-11]])

- Verify-token / signature validated **before** processing; bad token/signature → `403`, nothing stored.
- Throttle / rate limiter on `POST /webhooks/whatsapp`.

## Upload Contract (medium)

WhatsApp media (images, documents): MIME/extension whitelist, max size, tenant-scoped storage path via `core.files`. See [[../../core/file-storage/_module]].

## Tenant Isolation

`comms_whatsapp_config` (unique per company) + `comms_whatsapp_templates` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains queries. Webhook resolves company from `webhook_secret` / phone number then runs under `WithCompanyContext`. See [[../../../security/tenancy-isolation]].

## Related

- [[_module]] · [[../../../security/data-ownership]]
