---
domain: communications
module: sms-channel
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# SMS Channel — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.sms.view-any` | View SMS config + opt-out list (resource access) *(assumed — required by the `canAccess()` contract; previously only `manage` was listed)* |
| `comms.sms.manage` | Connect provider, credentials, test-send |

Messaging is gated by inbox permissions (`comms.inbox.reply`). Opt-outs are written by the STOP webhook via
`OptOutService` (no user verb). Seeded in `PermissionSeeder`. See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.sms.view-any')
        && BillingService::hasModule('comms.sms');
}
```

## Encrypted Fields

- `comms_sms_config.api_key`, `api_secret`, `webhook_secret` — all encrypted cast; write-only in UI. See [[../../../architecture/patterns/encryption]].

## Webhook Security (medium — [[../../../build/security-audit-2026-06-11]])

- Inbound + status webhook **signature-verified**.
- **Throttle / rate limiter** on `POST /webhooks/comms/sms`.
- **Test-send** header action calls the provider API — it carries the `panel-action` rate limiter ([[../../../architecture/security]]); outbound sends via the inbox inherit its send throttle.

## Compliance — Opt-out

`STOP` opt-outs are honoured **everywhere**: the driver throws `RecipientOptedOutException` and broadcast materialisation excludes opted-out numbers. `comms_sms_optouts.phone_e164` unique per company. This is a regulatory (TCPA / GDPR consent) control, not just a UX nicety.

## Tenant Isolation

`comms_sms_config` (unique per company) + `comms_sms_optouts` carry `company_id` (indexed). Webhook resolves company from `webhook_secret` / number, runs under `WithCompanyContext`. See [[../../../security/tenancy-isolation]].

## Related

- [[_module]] · [[../../../security/data-ownership]]
