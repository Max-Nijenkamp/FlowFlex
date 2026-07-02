---
domain: communications
module: whatsapp
feature: inbound-webhook
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Inbound Webhook

Provider webhook receives inbound WhatsApp messages + delivery/read receipts, normalises them, and hands them to the shared inbox.

## Behaviour

- `WhatsAppWebhookController` verifies the provider verify-token / signature **before** any processing — fail-closed (`403`) if invalid, storing nothing.
- Valid inbound → normalise payload → `InboundMessageData` → `InboxService::handleInbound` (inbox threads by E.164 phone number, writes the row).
- Delivery/read receipts → update `comms_messages.delivery_status` via the inbox.
- Endpoint is rate-limited.

## UI

- **Kind**: background
- **Trigger**: `POST /webhooks/whatsapp` (guest, signature-verified). No screen. Inbound messages surface in the [[../../shared-inbox/_module|Shared Inbox]].
- **Gating**: signature/verify-token (not user permission — public endpoint).

## Data

- Owns / writes: nothing — the message + conversation rows are written by the inbox via `InboxService`.
- Reads: provider webhook payload; `comms_whatsapp_config.webhook_secret` (own module) to verify.
- Cross-domain writes: none — this module never writes `comms_messages`; it hands normalised data to the inbox ([[../../../security/data-ownership]]).

## Relations

- Consumes: provider webhook (360dialog / Twilio / Meta).
- Feeds: `InboxService::handleInbound` (inbox owns + writes the message row); delivery-status updates flow to `comms_messages`.
- Shared entity: `comms_messages`, `comms_conversations` (owned by [[../../shared-inbox/_module|comms.inbox]]).

## Related

- [[../_module|WhatsApp]] · [[../architecture]] · [[../../shared-inbox/_module|Shared Inbox]]
