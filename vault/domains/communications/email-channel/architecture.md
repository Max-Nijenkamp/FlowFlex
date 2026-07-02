---
domain: communications
module: email-channel
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Channel — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `EmailChannelDriver implements ChannelDriverInterface` | `send(SendMessageData): void` | Send via Resend with `from` = channel address; inject the channel signature; set `References`/`In-Reply-To` threading headers. Registered with the inbox as the `email` driver. |
| `InboundCommsEmailController` | public (guest) endpoint | Signature-verified provider webhook; resolves the channel by `inbound_token`; drops spam-scored mail; parses HTML/plain → `InboundMessageData` → `InboxService::handleInbound`. |
| Threading resolver | (in driver/controller) | Maps `References`/`In-Reply-To` message-ids → conversation; fallback `(channel, from-address)` open conversation. |

**Driver rule:** the driver never writes `comms_messages` — it hands normalised inbound to the inbox, and for outbound it sends then lets the inbox record the row.

## Events

None fired or consumed. Cross-domain effect is via the inbox `ChannelDriver` contract, not events. See [[../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `EmailChannelResource` | Settings | #1 CRUD resource (own only) | Shows the forwarding address to configure; signature editor; test-connection action. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.email.view-any')
        && BillingService::hasModule('comms.email');
}
```

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Idempotency |
|---|---|---|---|
| Inbound parse job | default | per inbound webhook | `external_id` (Message-ID) dedupe done by inbox |

See [[../../../architecture/queue-jobs]] and [[../../../architecture/email]].

## Implementation Notes (tense-softened)

- v1 connection is designed around **forwarding** to a unique inbound address (`{token}@inbound.flowflex.io`) *(assumed)*; OAuth (Gmail/Outlook) is deferred to v1.x.
- The webhook is designed to **fail closed** on a bad signature and to **drop** mail over the spam-score threshold, logging the drop.
- Threading is designed to prefer header matching (`References`/`In-Reply-To`) with a subject + from-address fallback.

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/email]]
