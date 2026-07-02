---
domain: communications
module: whatsapp
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# WhatsApp — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `WhatsAppDriver implements ChannelDriverInterface` | `send(...): void` | Checks the 24h customer-service window (last inbound timestamp); free-form send inside the window, outside → `TemplateRequiredException`. Handles media send. Registered with the inbox as the `whatsapp` channel driver. |
| `WhatsAppWebhookController` | public (guest) endpoint | Verify-token / signature validated **before** any processing; normalises the provider payload → `InboundMessageData` → hands to `InboxService`; delivery/read receipts update `comms_messages.delivery_status`. |
| `SubmitTemplateAction` | `run(WhatsAppTemplate): void` | Submits a `draft` template to the provider for approval; sets status to `pending`, records `external_template_id`. |
| `SyncTemplateStatusJob` | queued job | Polls the provider for approval status; upserts by `external_template_id` (`pending → approved / rejected`). |
| `ConnectWhatsAppAction` | `run(config): void` | Verifies credentials with the provider **before** persisting `comms_whatsapp_config` (encrypts `api_key` + `webhook_secret`). |

**Driver rule:** `WhatsAppDriver` owns the send-time policy (window check, template requirement, media). It never writes `comms_messages` — it returns/normalises so the inbox writes the row.

## Events

None fired or consumed. See [[../../../architecture/event-bus]] for the platform contract; this module defines no cross-domain events. Cross-domain effect happens through the inbox `ChannelDriver` contract, not events.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `WhatsAppTemplateResource` | Settings | #1 Standard CRUD resource (own only) | Create + submit templates; approval-status badge; `{{n}}` placeholders. |
| `WhatsAppConfigPage` | Settings | #7 custom page (form) | Connect number, enter credentials (write-only display); `ConnectWhatsAppAction` verifies before save. |

Sending happens through the [[../shared-inbox/_module|Shared Inbox]] composer (template picker appears outside the 24h window).

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.whatsapp.view-any')
        && BillingService::hasModule('comms.whatsapp');
}
```

Custom pages state the same gate explicitly (per [[../../../architecture/filament-patterns]] #1).

## Jobs & Scheduling

| Job / Command | Queue | Trigger | Idempotency |
|---|---|---|---|
| `SyncTemplateStatusJob` | default | hourly | upsert by `external_template_id` |
| Webhook inbound processing | default | per webhook | `external_id` dedupe (done by inbox) |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None specified here. Inbound messages surface in the shared inbox, which owns any realtime/broadcast behaviour for conversations.

## Implementation Notes (tense-softened)

- The driver is designed to read the **last inbound timestamp** to decide whether a free-form send is allowed; outside the 24h window it raises `TemplateRequiredException` so the composer switches to the template picker.
- The webhook controller is designed to **fail closed**: a bad verify-token/signature returns `403` and stores nothing.
- Credentials are designed to be **verified with the provider before save**, so a broken connection never persists.
- Template status is designed to be **provider-driven**: local rows mirror provider approval, synced hourly and upserted by `external_template_id`.
