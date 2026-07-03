---
domain: communications
module: internal-messaging
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Internal Messaging

Team chat for internal communication — direct messages and group channels between company users. Slack-lite, inside FlowFlex.

> Standalone — does **not** use the shared-inbox conversation model. Internal users only, real-time via Reverb, its own tables.

## Module-key

`comms.internal`

**Priority:** p2  
**Panel:** comms  
**Permission prefix:** `comms.internal`  
**Tables:** `comms_channels_internal`, `comms_channel_members`, `comms_internal_messages`  
**Patterns:** websockets, custom-pages, search

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../core/file-storage/_module\|core.files]] | attachments |
| Hard | [[../../core/notifications/_module\|core.notifications]] | @mention notifications |

## Core Features

- Direct messages between users (DM channel auto-created per pair).
- Group channels: public (anyone joins) or private (invite-only).
- Channel record: name, description, type, members.
- Real-time messaging via Reverb (presence channel per chat channel — ui-strategy row #8).
- @mentions with notification.
- File attachments via Media Library.
- Message reactions (emoji).
- Threaded replies on a message.
- Unread indicators per channel (`last_read_at`).
- Search messages (Meilisearch), **filtered to channels the user belongs to**.
- **Visibility:** private channel + DM content readable by members only — a second scope on top of `CompanyScope`.

## See features/

- [[features/channels-dms|Channels & DMs]] — public/private channels + auto-created DMs with membership scope.
- [[features/realtime-messaging|Realtime Messaging]] — Reverb presence, typing whispers, unread indicators.
- [[features/threads-reactions|Threads & Reactions]] — threaded replies, emoji reactions, @mentions, search.

## Build Manifest

```
database/migrations/xxxx_create_comms_channels_internal_table.php
database/migrations/xxxx_create_comms_channel_members_table.php
database/migrations/xxxx_create_comms_internal_messages_table.php
app/Models/Comms/{InternalChannel,ChannelMember,InternalMessage}.php
app/Data/Comms/{CreateChannelData,PostMessageData}.php
app/Services/Comms/InternalChatService.php
app/Actions/Comms/{JoinChannelAction,InviteToChannelAction,MarkReadAction,ToggleReactionAction}.php
app/Events/Comms/InternalMessagePosted.php (ShouldBroadcast)
routes/channels.php (internal channel auth)
app/Filament/Comms/Pages/InternalMessagingPage.php
database/factories/Comms/{InternalChannelFactory,InternalMessageFactory}.php
tests/Feature/Comms/{InternalChatTest,ChannelVisibilityTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A users never see company B channels/messages; three-layer scope holds under `CompanyScope`.
- [ ] Module gating: messaging page hidden when `comms.internal` inactive.
- [ ] Non-member cannot read private channel/DM messages (query + channel auth + search).
- [ ] DM dedupe: second `dmWith` returns the same channel.
- [ ] @mention notifies; reaction toggles.
- [ ] Unread count from `last_read_at`.
- [ ] Public join works; private requires invite.
- [ ] Bodies purified; feed cursor-paginated.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Notifies | @mention notification | [[../../core/notifications/_module\|core.notifications]] | fires a notification (that module writes its own rows) |
| Uses | Media Library | [[../../core/file-storage/_module\|core.files]] | attachments |
| Internal broadcast | `InternalMessagePosted` (ShouldBroadcast, Reverb) | this module's UI | websocket-only event on the channel presence channel — **not** a cross-domain domain-event |

No cross-domain **domain events** on the event bus (see [[../../../architecture/event-bus]]).

**Data ownership:** `comms.internal` writes **only** its three tables. @mention notifications are delivered by `core.notifications`, which writes its **own** notification rows in reaction — internal-messaging never writes notification/file tables directly ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../shared-inbox/_module|Shared Inbox]] (separate model) · [[../../../architecture/websockets]]
