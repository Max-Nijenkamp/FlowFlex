---
domain: communications
module: shared-inbox
feature: unified-conversation-view
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Unified Conversation View

The three-panel inbox: conversation list (all channels) → message thread → composer + contact/context rail. The hub agents live in.

## Behaviour

- Conversation list aggregates every connected channel, sorted by `last_message_at`, filterable by status (open / pending / resolved / snoozed), assignee, and channel.
- Selecting a conversation opens its message thread (inbound/outbound + internal notes visually distinct).
- Composer is channel-aware: capabilities come from the resolved `ChannelDriver` (e.g. WhatsApp shows the template picker outside the 24h window).
- Channel badge per conversation; tags via `spatie/laravel-tags`; contact card auto-linked from CRM when present.
- New messages arrive live via Reverb (`company.{id}.comms`).

## UI

- **Kind**: custom-page
- **Page**: "Shared Inbox" (`/comms/inbox`) — ui-strategy row #8.
- **Layout**: three panels — left conversation list, centre thread, right context rail (contact, tags, assignee, status).
- **Key interactions**: select conversation → load thread; type reply → `InboxService::send` via driver → optimistic append; assign / set-status / snooze from the header; internal-note toggle.
- **States**: empty (no channels connected → "connect a channel" CTA) · loading (skeleton list + thread) · error (send failure toast + retry) · selected (active conversation highlighted, thread + rail populated).
- **Gating**: `comms.inbox.view-any`; reply needs `comms.inbox.reply`; assign needs `comms.inbox.assign`.

## Data

- Owns / writes: `comms_conversations`, `comms_messages` (own module) via `InboxService`.
- Reads: CRM contact by email/phone via `ContactService` (read-only, [[../../crm/contacts/_module]]).
- Cross-domain writes: none — contact linking is read-only; no other domain's tables are written ([[../../../security/data-ownership]]).

## Relations

- Consumes: channel drivers' normalised `InboundMessageData` (email/whatsapp/sms modules).
- Feeds: `MessageReceived` Reverb broadcast (UI only, not a bus event).
- Shared entity: `crm_contacts` (owned by CRM, read-only auto-link).

## Test Checklist

### Unit
- [ ] Composer capabilities derive from the resolved `ChannelDriver::capabilities()` (WhatsApp shows template picker outside the 24h window)
- [ ] List sort/filter: conversations ordered by `last_message_at`, filtered by status/assignee/channel

### Feature (Pest)
- [ ] Sending a reply routes through `InboxService::send` → correct driver and appends an outbound message
- [ ] Internal note is stored but never dispatched to a driver
- [ ] Tenant isolation: agent cannot open a conversation from another company (404/denied)

### Livewire
- [ ] Reply action denied without `comms.inbox.reply`; assign denied without `comms.inbox.assign`
- [ ] Live arrival: `MessageReceived` broadcast appends a new message to the open thread
- [ ] Empty state shows the "connect a channel" CTA when no channel is connected

## Related

- [[../_module|Shared Inbox]] · [[../architecture]] · [[collision-detection]] · [[../../../architecture/websockets]]
