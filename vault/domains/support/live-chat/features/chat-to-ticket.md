---
domain: support
module: live-chat
feature: chat-to-ticket
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Chat-to-Ticket

Convert a live chat into a ticket — explicitly by an agent, or automatically when everyone is offline.

## Behaviour

- Offline capture: `ChatService::start` with no agents online creates a `missed` chat + a ticket via `TicketService` (the visitor's message becomes the ticket description).
- Explicit convert: `ConvertChatToTicketAction::run(chatId)` builds a ticket from the transcript and links `sup_chats.ticket_id`.
- Requester is matched to a CRM contact by email where possible (soft).

## UI

- **Kind**: custom-page (embedded action) — a "Convert to ticket" action inside [[./agent-queue|Agent Queue]]; offline capture has no UI.
- **Page**: action within `ChatQueuePage` (`/support/chat`).
- **Layout**: action button on the conversation pane → confirm → ticket created, chat shows a "converted → T-xxxx" link.
- **Key interactions**: click convert → transcript packaged → ticket created via `TicketService` → link shown.
- **States**: empty (n/a) · loading (creating ticket) · error (creation fails → retry) · selected (chat with a ticket link banner).
- **Gating**: `support.chat.respond` (+ ticket creation gated by `TicketService` internally).

## Data

- Owns / writes: `sup_chats` (`ticket_id`, `status=missed/ended`).
- Reads: `crm_contacts` (requester match, soft).
- Cross-domain writes: none — the ticket is created through `TicketService`; support.tickets owns `sup_tickets` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: offline condition / agent action.
- Feeds: a new ticket in [[../../tickets/_module|support.tickets]] (which may fire `TicketResolved` later).
- Shared entity: `sup_tickets` (created via service), `crm_contacts` (read).

## Unknowns

- Whether transcript is attached as description vs an internal note *(assumed: description)* — [[../unknowns]].

## Related

- [[../_module|Live Chat]] · [[./agent-queue]] · [[../../tickets/_module|support.tickets]]
