---
domain: communications
module: automations
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Automations — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.automations.view-any` | View rules + flows |
| `comms.automations.manage` | Create/edit rules + chatbot flows |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.automations.view-any')
        && BillingService::hasModule('comms.automations');
}
```

## Tenant Isolation

`comms_automation_rules` + `comms_chatbot_flows` carry `company_id` (indexed) via `BelongsToCompany`. The engine runs inside the inbound handler under the conversation's company context. See [[../../../security/tenancy-isolation]].

## Abuse / Safety

- **Loop guard** (integrity): automation-sent replies are system-actor stamped and never re-enter `onInbound` — prevents auto-reply storms.
- **Away-message throttle**: once per conversation per day *(assumed)* — prevents outbound spam.
- Auto-reply bodies are purified (they become inbox messages via `InboxService`).

## Encrypted Fields

None.

## Related

- [[_module]] · [[../../../security/data-ownership]]
