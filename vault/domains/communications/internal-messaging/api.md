---
domain: communications
module: internal-messaging
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Internal Messaging — API / DTOs

## DTOs

### `CreateChannelData` (input)

| Field | Type | Rules |
|---|---|---|
| `name` | string | required for non-DM |
| `type` | enum | in: dm, public, private |
| `member_ids` | array | required for private |

### `PostMessageData` (input)

| Field | Type | Rules |
|---|---|---|
| `channel_id` | ulid | required, current user must be a member |
| `body` | text | required, max:4000, purified |
| `parent_message_id` | ulid nullable | same channel (thread) |
| `attachments` | array | Media Library, MIME/size contract |

## Service surface (internal)

| Method | Kind | Notes |
|---|---|---|
| `InternalChatService::dmWith(userId): ChannelData` | command | find-or-create DM |
| `InternalChatService::post(PostMessageData): MessageData` | command | persist + broadcast + notify |
| `JoinChannelAction / InviteToChannelAction / MarkReadAction / ToggleReactionAction` | command | membership + read + reactions |

## Realtime / Channel Auth

`routes/channels.php` — a membership check authorises the Reverb presence channel per internal channel. See [[../../../architecture/websockets]].

## Public / Portal Endpoints

None — internal users only.

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/patterns/dto-pattern]]
