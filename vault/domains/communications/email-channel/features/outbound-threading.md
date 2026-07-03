---
domain: communications
module: email-channel
feature: outbound-threading
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Outbound Threading

Replies send from the connected address with the channel signature and threading headers, so the customer's mail client threads them correctly.

## Behaviour

- `EmailChannelDriver::send` sends via Resend with `from` = the channel `address`.
- Injects the channel `signature` (purified HTML).
- Sets `References` / `In-Reply-To` headers derived from the conversation's prior message-ids, so replies thread on the recipient side.
- Outbound attachments included.

## UI

- **Kind**: widget (composer behaviour inside the [[../../shared-inbox/features/unified-conversation-view|Shared Inbox]] — no standalone page). Signature is edited in `EmailChannelResource`.
- **Layout**: standard reply composer; signature appended.
- **Key interactions**: type reply → send → driver sets headers + from + signature.
- **States**: default · sending · error (send failure toast + retry).
- **Gating**: `comms.inbox.reply`.

## Data

- Owns / writes: `comms_email_channels.signature` (own module, via resource).
- Reads: conversation message-ids (from inbox) for threading headers.
- Cross-domain writes: none — the outbound message row is written by the inbox via `InboxService`, not this module ([[../../../security/data-ownership]]).

## Relations

- Consumes: prior conversation message-ids (inbox).
- Feeds: outbound send via `InboxService::send` → driver.
- Shared entity: `comms_messages` (owned by [[../../shared-inbox/_module|comms.inbox]]).

## Test Checklist

### Unit
- [ ] `References` / `In-Reply-To` headers built from the conversation's prior message-ids
- [ ] Channel signature (purified HTML) appended to the outbound body

### Feature (Pest)
- [ ] `EmailChannelDriver::send` sends from the channel `address` with threading headers set (fake Resend)
- [ ] Reply on an existing conversation threads onto it; outbound attachments included

### Livewire
- [ ] Composer send is denied without `comms.inbox.reply`; send failure surfaces a retry toast

## Related

- [[../_module|Email Channel]] · [[inbound-parsing]] · [[../../shared-inbox/_module|Shared Inbox]]
