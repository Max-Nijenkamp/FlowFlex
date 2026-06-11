---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.internal
status: planned
priority: p2
depends-on: [core.billing, core.rbac, core.files, core.notifications]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [websockets, custom-pages, search]
tables: [comms_channels_internal, comms_channel_members, comms_internal_messages]
permission-prefix: comms.internal
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Internal Messaging

Team chat for internal communication — direct messages and group channels between company users. Slack-lite, inside FlowFlex.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, attachments, @mention notifications |

(Standalone — does NOT use the shared-inbox conversation model; internal users only.)

---

## Core Features

- Direct messages between users (DM channel auto-created per pair)
- Group channels: public (anyone joins) or private (invite-only)
- Channel record: name, description, type, members
- Real-time messaging via Reverb (presence channel per chat channel — ui-strategy row #8)
- @mentions with notification
- File attachments via Media Library
- Message reactions (emoji)
- Threaded replies on a message
- Unread indicators per channel (`last_read_at`)
- Search messages (Meilisearch)
- **Visibility**: private channel + DM content readable by members only — second-layer scope on top of CompanyScope

---

## Data Model

### comms_channels_internal

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string nullable | null for DMs |
| description | text nullable | |
| type | string | dm / public / private |
| dm_key | string nullable unique | sorted user-id pair hash (DM dedupe) |
| created_by | ulid FK users | |
| deleted_at | timestamp nullable | |

### comms_channel_members — id, channel_id FK, company_id, user_id FK, last_read_at nullable; unique `(channel_id, user_id)`
### comms_internal_messages

| Column | Type | Notes |
|---|---|---|
| id, channel_id FK, company_id (indexed) | ulid | |
| user_id | ulid FK | |
| body | text | purified, max 4000 |
| parent_message_id | ulid nullable FK self | threads |
| reactions | jsonb default `{}` | {emoji: [user_ids]} |
| deleted_at | timestamp nullable | |

**Indexes:** `(channel_id, created_at)` (cursor-paginated feed)

---

## DTOs

### CreateChannelData — name (required for non-DM), type (in set), member_ids[] (private)
### PostMessageData — channel_id (member), body (required, max:4000), parent_message_id? (same channel), attachments[]

## Services & Actions

- `InternalChatService::dmWith(string $userId): ChannelData` — find-or-create by dm_key
- `InternalChatService::post(PostMessageData $data): MessageData` — persists, broadcasts, @mention notifications
- `JoinChannelAction` (public only) / `InviteToChannelAction` (private, member) / `MarkReadAction`
- `ToggleReactionAction`
- Channel auth (`routes/channels.php`): membership check per internal channel

---

## Filament

**Nav group:** Messaging

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `InternalMessagingPage` | #8 chat custom page | channel sidebar + thread pane; Reverb presence + typing whispers; cursor-paginated history |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('comms.internal.view-any') && BillingService::hasModule('comms.internal')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): Specify MIME/extension whitelist, max size, and tenant-scoped storage path for chat attachments.

---

## Permissions

`comms.internal.use` (all users by default) · `comms.internal.manage-channels`

---

## Search & Realtime

Meilisearch: message bodies — **search results filtered to channels the user is a member of** (post-filter on membership *(assumed)*). Realtime: Reverb presence channel per internal channel.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Non-member cannot read private channel/DM messages (query + channel auth + search)
- [ ] DM dedupe: second dmWith returns same channel
- [ ] @mention notifies; reaction toggles
- [ ] Unread count from last_read_at
- [ ] Public join works; private requires invite
- [ ] Bodies purified; feed cursor-paginated

---

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

---

## Related

- [[domains/communications/shared-inbox]]
- [[architecture/websockets]]
