---
domain: support
module: live-chat
feature: chat-widget
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Chat Widget

The embeddable, public-facing chat bubble on a customer's website.

## Behaviour

- Script tag + company widget key embeds a built JS widget (`resources/js/chat-widget/`).
- Visitor opens the bubble → `ChatService::start` (validates widget key); a per-chat signed token is issued.
- Messages send/receive over Reverb `chat.{chat_id}` (visitor token auth); typing/read receipts via whispers.
- Offline (no agents online) → the message is captured as a ticket instead.
- Visitor context: name/email (optional), page URL, user-agent.

## UI

- **Kind**: public-vue — external unauthenticated widget, built JS embed (Vue-based), ui-strategy row #16 / #8 realtime.
- **Page**: chat bubble injected on the customer site; served via `GET /chat/widget.js` (`ChatWidgetController`).
- **Layout**: collapsed bubble → expanded panel (message list, composer, optional pre-chat name/email form).
- **Key interactions**: open → start chat; type → send (optimistic + broadcast); agent typing indicator; offline → "leave a message" (creates ticket).
- **States**: empty (fresh chat → greeting) · loading (connecting to Reverb) · error (invalid widget key / rate-limited → graceful fallback) · selected (active conversation).
- **Gating**: none for the visitor (scoped widget-key + per-chat token); no panel session.

## Data

- Owns / writes: `sup_chats`, `sup_chat_messages`.
- Reads: `crm_contacts` via `ContactService` (email match, soft).
- Cross-domain writes: none — offline capture creates a ticket via `TicketService` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (public entry point).
- Feeds: `ChatStarted` → queue; offline → ticket in [[../../tickets/_module|support.tickets]].
- Shared entity: `crm_contacts` (visitor email match).

## Unknowns

- Widget theming / white-label; presence concurrency ceiling — [[../unknowns]].

## Related

- [[../_module|Live Chat]] · [[./agent-queue]] · [[../security|Live Chat Security]]
