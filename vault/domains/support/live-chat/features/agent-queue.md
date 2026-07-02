---
domain: support
module: live-chat
feature: agent-queue
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Agent Queue

The in-panel real-time console where agents pick up and answer live chats.

## Behaviour

- Incoming chats distribute to available agents (least-active-chats *(assumed)*); agents set availability online/away/offline.
- Live conversation pane with typing indicators + read receipts (whisper events).
- Canned responses insertable (soft-dep on [[../../canned-responses/_module|support.canned]]).
- Convert a chat to a ticket in one click (see [[./chat-to-ticket]]).

## UI

- **Kind**: custom-page — `ChatQueuePage`, bespoke multi-pane realtime console (not table+form).
- **Page**: "Chat Queue" (`/support/chat`) — Filament custom Page + Reverb, ui-strategy row #8; availability toggle via a panel render hook in the header.
- **Layout**: left = active/waiting chats list; centre = conversation + composer; right = visitor info (page, browser, contact, history).
- **Key interactions**: claim a waiting chat; type reply (broadcast); see visitor typing; mark read; insert canned; convert to ticket.
- **States**: empty (no active chats → "you're all caught up") · loading (connecting) · error (send fails → retry) · selected (active chat highlighted, conversation loaded).
- **Gating**: `support.chat.respond`.

## Data

- Owns / writes: `sup_chats` (assignment, status), `sup_chat_messages`, `sup_agent_availability`.
- Reads: `crm_contacts` (visitor context); canned responses (soft).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `ChatStarted` / `ChatMessageSent` (same-domain broadcasts).
- Feeds: `ConvertChatToTicketAction` → ticket in support.tickets.
- Shared entity: `crm_contacts` (read), `users` availability.

## Unknowns

- Assignment algorithm (least-active vs skills) *(assumed)* — [[../unknowns]].

## Related

- [[../_module|Live Chat]] · [[./chat-widget]] · [[./chat-to-ticket]]
