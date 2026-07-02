---
domain: communications
module: email-channel
feature: inbound-parsing
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Inbound Parsing

Incoming emails become shared-inbox conversations: verify → resolve channel → drop spam → parse → hand to inbox.

## Behaviour

1. Provider posts the inbound email to the webhook; signature verified (fail-closed).
2. Resolve the channel by `inbound_token`; unknown token → drop.
3. Spam-score header over threshold → drop + log.
4. Parse HTML (purified) + plain text, attachments, headers → `InboundMessageData`.
5. `InboxService::handleInbound` threads it (headers → subject/from fallback) and writes the row.

## UI

- **Kind**: background
- **Trigger**: `POST /webhooks/comms/email/inbound` (guest, signature-verified). No screen — messages surface in the [[../../shared-inbox/_module|Shared Inbox]]. Channel setup lives in `EmailChannelResource`.
- **Gating**: signature/token (public endpoint).

## Data

- Owns / writes: nothing — the message/conversation rows are written by the inbox.
- Reads: `comms_email_channels` (own module) to resolve token + verify.
- Cross-domain writes: none — never writes `comms_messages` directly; hands normalised data to the inbox ([[../../../security/data-ownership]]).

## Relations

- Consumes: provider inbound webhook.
- Feeds: `InboxService::handleInbound` (inbox owns the row).
- Shared entity: `comms_messages`, `comms_conversations` (owned by [[../../shared-inbox/_module|comms.inbox]]).

## Related

- [[../_module|Email Channel]] · [[outbound-threading]] · [[../architecture]]
