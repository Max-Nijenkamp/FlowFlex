---
domain: communications
module: sms-channel
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# SMS Channel — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `SmsDriver implements ChannelDriverInterface` | `send(SendMessageData): void` | Throws `RecipientOptedOutException` if the number is opted out; estimates segments; records cost; sends via provider. Registered as the `sms` driver. |
| `SmsWebhookController` | public (guest) endpoint | Signature-verified. Inbound `STOP` → opt-out row + confirmation *(assumed: provider handles confirmation)*; normal inbound → `InboundMessageData` → inbox; status callbacks update `comms_messages.delivery_status`. |
| `OptOutService::isOptedOut` | `isOptedOut(string $e164): bool` | Checked by the driver **and** by broadcast recipient materialisation. |

**Driver rule:** the driver enforces opt-out + records cost, but never writes `comms_messages` — the inbox writes the row from normalised data.

## Events

None fired or consumed. Cross-domain effect is via the inbox driver contract + `OptOutService` read API, not events. See [[../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `SmsChannelResource` | Settings | #1 CRUD resource | Connect provider, credentials write-only, test send. |
| Opt-out list (relation/page) | Settings | #1 read-only | Compliance view of opted-out numbers. |

Sending happens through the [[../shared-inbox/_module|Shared Inbox]] composer (segment counter shown).

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.sms.view-any')
        && BillingService::hasModule('comms.sms');
}
```

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Idempotency |
|---|---|---|---|
| Inbound/status webhook processing | default | per webhook | `external_id` dedupe (inbox) |

See [[../../../architecture/queue-jobs]].

## Implementation Notes (tense-softened)

- The driver is designed to **block opted-out numbers everywhere** — a single `OptOutService` check is shared by the driver and broadcast materialisation.
- The webhook is designed to be **signature-verified** and to treat a `STOP` inbound as an opt-out event rather than a normal message.
- Cost is designed to be recorded from provider callbacks into `comms_messages.meta.cost_cents` *(assumed meta column)* using `brick/money`.

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../broadcast/_module|Broadcast]]
