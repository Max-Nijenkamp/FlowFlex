---
domain: communications
module: shared-inbox
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Shared Inbox

Unified team inbox aggregating email, WhatsApp, SMS (Instagram/Facebook later *(assumed: post-P2)*) messages into one conversation view. Team members collaborate on replies. The hub of the Communications domain — build first in `/comms`; channel modules plug into it via a channel-agnostic driver registry.

## Module-key

`comms.inbox`

**Priority:** p2  
**Panel:** comms  
**Permission prefix:** `comms.inbox`  
**Tables:** `comms_channels`, `comms_conversations`, `comms_messages`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()` |
| Hard | [[../../core/file-storage/_module\|core.files]] | Message attachments |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | Inbound processing + reopen jobs |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | Auto-link contact by email/phone (find-or-create, read-only) |
| Soft | comms.email / comms.whatsapp / comms.sms | The actual channels — inbox is empty without at least one connected |
| Soft | [[../automations/_module\|comms.automations]] | Routing / auto-reply on inbound |

## Core Features

- Unified conversation list across all connected channels.
- Conversation = thread of messages with one contact across one channel.
- Assignment: assign a conversation to a team member.
- Status: open / pending / resolved / snoozed.
- Internal notes on conversations (never sent to the customer).
- Collision detection: warn when another agent is replying to the same conversation (Reverb whisper).
- Channel badges: visual indicator of source channel per conversation.
- Contact linking: auto-link to CRM contact by email/phone.
- Conversation tags via spatie/laravel-tags.
- Search across all conversations (Meilisearch).
- Snooze a conversation until a later time (auto-reopen).
- **Channel driver contract**: each channel module registers a `ChannelDriver` (send, normalise-inbound, capabilities) — inbox stays channel-agnostic.

## See features/

- [[features/unified-conversation-view|Unified Conversation View]] — the three-panel inbox custom page.
- [[features/channel-driver-registry|Channel Driver Registry]] — channel modules register drivers in their providers.
- [[features/collision-detection|Collision Detection]] — Reverb whisper warns of concurrent replies.
- [[features/snooze-reopen|Snooze & Reopen]] — scheduled auto-reopen + immediate reopen on inbound.

## Build Manifest

```
database/migrations/xxxx_create_comms_channels_table.php
database/migrations/xxxx_create_comms_conversations_table.php
database/migrations/xxxx_create_comms_messages_table.php
app/Models/Comms/{Channel,Conversation,Message}.php
app/Data/Comms/{SendMessageData,InboundMessageData,MessageData,ConversationData}.php
app/Contracts/Comms/{InboxServiceInterface,ChannelDriverInterface}.php
app/Services/Comms/InboxService.php
app/Support/Comms/ChannelDriverRegistry.php
app/Providers/Comms/CommsServiceProvider.php
app/Jobs/Comms/ProcessInboundMessageJob.php
app/Console/Commands/Comms/ReopenSnoozedCommand.php
app/Events/Comms/MessageReceived.php (ShouldBroadcast)
app/Filament/Comms/Pages/SharedInboxPage.php
app/Filament/Comms/Resources/ChannelResource.php
database/factories/Comms/{ChannelFactory,ConversationFactory,MessageFactory}.php
tests/Feature/Comms/{InboxThreadingTest,InboxSendTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see/reply/assign company B conversations; inbound webhook resolves company from the channel before writing.
- [ ] Module gating: inbox page + channel resource hidden when `comms.inbox` inactive.
- [ ] Inbound threads onto existing open conversation per `(channel, party)`; new otherwise.
- [ ] `external_id` dedupe (webhook retry = one message).
- [ ] Internal note never sent through driver.
- [ ] Send routes through correct driver; inactive channel rejected.
- [ ] Snooze hides + auto-reopens; inbound reopens immediately.
- [ ] Contact auto-link by email/phone when CRM active.
- [ ] Broadcast event on arrival; bodies purified.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | contact read API | [[../../crm/contacts/_module\|crm.contacts]] | Auto-link conversation by email/phone (find-or-create, read-only) |
| Triggers | inbound hand-off (soft) | [[../automations/_module\|comms.automations]] | Routing / auto-reply on inbound; inbox fires **no** domain event — automations subscribe via their own soft integration *(assumed)* |
| Internal broadcast | `MessageReceived` (ShouldBroadcast, Reverb) | inbox UI only | Websocket-only realtime event on `company.{id}.comms` — **not** a cross-domain domain-event |

**Data ownership:** `comms.inbox` writes only `comms_channels`, `comms_conversations`, `comms_messages`. It has no fired/consumed cross-domain domain-events (per source). Cross-domain effects are read-only (CRM contact lookup) or handled by the interested domain's own soft integration; never a direct write into another domain's tables ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../whatsapp/_module]] · [[../email-channel/_module]] · [[../sms-channel/_module]] · [[../automations/_module|comms.automations]]
- [[../../crm/contacts/_module|Contacts]] · [[../../../architecture/websockets]]
