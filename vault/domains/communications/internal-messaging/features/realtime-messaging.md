---
domain: communications
module: internal-messaging
feature: realtime-messaging
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Realtime Messaging

Live message delivery, presence, typing indicators, and unread counts via Reverb.

## Behaviour

- `InternalChatService::post` persists then broadcasts `InternalMessagePosted` on the channel's presence channel.
- Typing indicators via client whispers; presence shows who's online in a channel.
- Unread count derived from `last_read_at`; `MarkReadAction` clears it.
- Feed is cursor-paginated on `(channel_id, created_at)`.

## UI

- **Kind**: custom-page
- **Page**: `InternalMessagingPage` (`/comms/messaging`) — Messaging nav group, ui-strategy row #8.
- **Layout**: channel sidebar + thread pane + composer; presence avatars + typing line.
- **Key interactions**: type (whisper typing) → send → optimistic append + broadcast; scroll up → load older (cursor); focus channel → mark read.
- **States**: empty (no messages → prompt) · loading (skeleton feed) · error (send fail → retry) · selected (active channel + live updates).
- **Gating**: `comms.internal.use`; Reverb channel auth requires membership.

## Data

- Owns / writes: `comms_internal_messages`, `comms_channel_members.last_read_at` (own module).
- Reads: nothing cross-domain.
- Cross-domain writes: none — @mention notification is delivered by `core.notifications` (its own rows) ([[../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `InternalMessagePosted` Reverb broadcast (UI only); @mention → `core.notifications`.
- Shared entity: none owned elsewhere (except `users`).

## Related

- [[../_module|Internal Messaging]] · [[channels-dms]] · [[../../../architecture/websockets]]
