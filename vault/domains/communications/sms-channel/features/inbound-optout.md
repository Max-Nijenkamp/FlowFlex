---
domain: communications
module: sms-channel
feature: inbound-optout
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Inbound & Opt-out

Inbound SMS lands in the inbox; a `STOP` keyword unsubscribes the number and is honoured everywhere.

## Behaviour

- Signature-verified webhook receives inbound SMS.
- `STOP` (and synonyms *(assumed)*) → create `comms_sms_optouts` row (`opted_out_at`), provider sends confirmation *(assumed)*.
- Any other inbound → normalise → `InboxService::handleInbound`, threaded by E.164.
- Opted-out numbers are blocked from all subsequent sends (inbox + broadcast) via `OptOutService`.

## UI

- **Kind**: background (webhook) + a read-only opt-out compliance list under `SmsChannelResource`.
- **Trigger**: `POST /webhooks/comms/sms` (guest, signature-verified). Inbound messages surface in the [[../../shared-inbox/_module|Shared Inbox]].
- **Gating**: opt-out list view under `comms.sms.manage`; webhook is signature-gated.

## Data

- Owns / writes: `comms_sms_optouts` (own module).
- Reads: provider webhook payload; `comms_sms_config.webhook_secret` to verify.
- Cross-domain writes: none — the message row is written by the inbox, not this module ([[../../../security/data-ownership]]).

## Relations

- Consumes: provider inbound webhook.
- Feeds: `InboxService::handleInbound` (inbox owns the row); `OptOutService::isOptedOut` consumed by [[../../broadcast/_module|comms.broadcast]].
- Shared entity: `comms_messages`, `comms_conversations` (owned by [[../../shared-inbox/_module|comms.inbox]]).

## Related

- [[../_module|SMS Channel]] · [[outbound-send]] · [[../architecture]]
