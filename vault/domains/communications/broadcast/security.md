---
domain: communications
module: broadcast
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Broadcast — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.broadcast.view-any` | View broadcasts + stats |
| `comms.broadcast.create` | Create/edit draft broadcasts |
| `comms.broadcast.send` | Schedule / send a broadcast |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.broadcast.view-any')
        && BillingService::hasModule('comms.broadcast');
}
```

The send action is additionally gated on `comms.broadcast.send`.

## Rate Limiting (medium — [[../../../build/security-audit-2026-06-11]])

- Delivery/open **webhook callbacks** (on the channel modules) must be rate-limited.
- Outbound batch sending is throttled per channel (chunk ~100/min *(assumed)*).

## Compliance

- SMS opt-outs excluded at materialisation via `OptOutService`.
- Undeliverable emails (`email_deliverable=false`) excluded.
- WhatsApp broadcasts require an **approved** template.

## Tenant Isolation

`comms_broadcasts` + `comms_broadcast_recipients` carry `company_id` (indexed). Audience reads (segments, employee groups) run within the acting company. Batch jobs run under `WithCompanyContext`. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None.

## Related

- [[_module]] · [[../../../security/data-ownership]]
