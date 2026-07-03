---
domain: communications
module: broadcast
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Broadcast — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.broadcast.view-any` | View broadcasts + stats |
| `comms.broadcast.create` | Create/edit draft broadcasts |
| `comms.broadcast.send` | Schedule / send a broadcast (`draft → scheduled`) |
| `comms.broadcast.cancel` | Cancel a `draft` / `scheduled` broadcast *(assumed — the cancel transition had no explicit verb)* |

**Verb-per-command:** `send` covers `draft → scheduled`; `scheduled → sending → sent / failed` are driven by the
scheduler / batch jobs (system, no user verb). Seeded in `PermissionSeeder`. See [[../../../security/authn-authz]].

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

- The **send / schedule** panel action is mass outbound comms — it carries the `panel-action` rate limiter ([[../../../architecture/security]]).
- Outbound batch sending is throttled per channel on the `notifications` queue (chunk ~100/min *(assumed)*), keeping sends within provider throughput.
- Delivery/open **webhook callbacks** (on the channel modules) must be rate-limited.

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
