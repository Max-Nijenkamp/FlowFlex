---
tags: [flowflex, domain/communications, messaging, chat, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-07
---

# Internal Messaging & Chat

Real-time channels and direct messages. The Slack alternative for teams that want everything in one place — no third-party messaging tool required.

**Who uses it:** All employees (tenants)
**Filament Panel:** `communications`
**Depends on:** Core, [[File Storage]], Pusher (real-time)
**Phase:** 5
**Build complexity:** Very High — 3 resources, 2 pages, 4 tables

---

## Features

- **Public and private channels** — create topic channels; public channels are discoverable by all tenants; private channels require invitation
- **Direct messages** — one-to-one and group DMs between tenants; type = `direct` with member list
- **Threaded replies** — reply to a specific message in-thread; keeps channels clean for high-traffic topics
- **File sharing** — attach files from the file library or upload directly; stored to S3 via FileStorageService; previewed inline
- **Message search** — full-text search across message history the tenant has access to; search by keyword, channel, or sender
- **Emoji reactions** — react to any message with emoji; reaction counts shown inline
- **Message editing and deletion** — edit or soft-delete own messages; `is_edited` flag shown; deleted content replaced with "[deleted]"
- **Real-time delivery** — messages delivered via Pusher WebSocket; no polling; presence indicators show who's online
- **Task and record linking** — paste a FlowFlex URL (task, deal, invoice) and it auto-expands as a rich link preview
- **Channel archiving** — archive old channels; archived channels remain searchable but no new messages accepted
- **@mentions and notifications** — @mention a tenant to push an in-app notification; @here for all active members; @channel for all
- **Message pinning** — pin important messages in a channel; pinned messages listed in channel sidebar
- **Read state tracking** — track last-read message per tenant per channel for unread badge counts

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `chat_channels`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `type` | enum | `public`, `private`, `direct` |
| `description` | string nullable | |
| `is_archived` | boolean default false | |
| `last_message_at` | timestamp nullable | |
| `created_by` | ulid FK nullable | → tenants |

### `chat_channel_members`
| Column | Type | Notes |
|---|---|---|
| `chat_channel_id` | ulid FK | → chat_channels |
| `tenant_id` | ulid FK | → tenants |
| `role` | enum | `member`, `admin` |
| `joined_at` | timestamp | |
| `last_read_message_id` | ulid FK nullable | → chat_messages |
| `is_muted` | boolean default false | |

### `chat_messages`
| Column | Type | Notes |
|---|---|---|
| `chat_channel_id` | ulid FK | → chat_channels |
| `tenant_id` | ulid FK | author → tenants |
| `body` | text | message content |
| `type` | enum | `text`, `file`, `system` |
| `parent_message_id` | ulid FK nullable | → chat_messages (thread parent) |
| `is_edited` | boolean default false | |
| `edited_at` | timestamp nullable | |
| `is_deleted` | boolean default false | |
| `sent_at` | timestamp | |
| `reactions` | json nullable | {emoji: [tenant_id, ...]} |
| `pinned_at` | timestamp nullable | |

### `chat_message_attachments`
| Column | Type | Notes |
|---|---|---|
| `chat_message_id` | ulid FK | → chat_messages |
| `file_id` | ulid FK | → files |
| `file_name` | string | |
| `file_size` | integer | bytes |
| `mime_type` | string nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `MessageSent` | `chat_message_id`, `channel_id` | Real-time Pusher broadcast to channel members |
| `ChannelCreated` | `chat_channel_id` | Activity log |

---

## Events Consumed

None — Chat is self-contained and event-driven via Pusher.

---

## Permissions

```
communications.chat-channels.view
communications.chat-channels.create
communications.chat-channels.edit
communications.chat-channels.delete
communications.chat-channels.archive
communications.chat-messages.view
communications.chat-messages.send
communications.chat-messages.delete-own
communications.chat-messages.delete-any
communications.chat-messages.pin
```

---

## Related

- [[Communications Overview]]
- [[Company Announcements]]
- [[Notifications & Alerts]]
- [[File Storage]]
